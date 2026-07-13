<?php

namespace App\Http\Controllers\Gateway;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Lib\ReferralCommission;
use App\Lib\VirtualCardLib;
use App\Models\AdminNotification;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Models\Transaction;
use App\Models\User;
use App\Models\VirtualCard;
use App\Models\Wallet;
use Illuminate\Http\Request;

class PaymentController extends Controller {
    public function deposit() {
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderby('name')->get();
        $pageTitle = 'Deposit Money';
        $wallet = null;
        if(request('wallet_id')) {
            $wallet = Wallet::where('user_id', auth()->id())->findOrFail(request('wallet_id'));
        }
        return view('Template::user.payment.deposit', compact('gatewayCurrency', 'pageTitle', 'wallet'));
    }

    public function depositInsert(Request $request) {
        $request->validate([
            'wallet_id' => 'nullable|integer|exists:wallets,id',
            'amount' => 'required|numeric|gt:0',
            'gateway' => 'required',
            'currency' => 'required',
        ]);

        $user = auth()->user();
        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->where('method_code', $request->gateway)->where('currency', $request->currency)->first();

        if($request->wallet_id) {
            Wallet::where('user_id', $user->id)->findOrFail($request->wallet_id);
        }

        if (!$gate) {
            $notify[] = ['error', 'Invalid gateway'];
            return back()->withNotify($notify);
        }

        if ($gate->min_amount > $request->amount || $gate->max_amount < $request->amount) {
            $notify[] = ['error', 'Please follow deposit limit'];
            return back()->withNotify($notify);
        }

        $charge = $gate->fixed_charge + ($request->amount * $gate->percent_charge / 100);
        $payable = $request->amount + $charge;
        $finalAmount = $payable * $gate->rate;

        $data = new Deposit();
        $data->user_id = $user->id;
        $data->wallet_id = $request->wallet_id;
        $data->method_code = $gate->method_code;
        $data->method_currency = strtoupper($gate->currency);
        $data->amount = $request->amount;
        $data->charge = $charge;
        $data->rate = $gate->rate;
        $data->final_amount = $finalAmount;
        $data->btc_amount = 0;
        $data->btc_wallet = "";
        $data->trx = getTrx();
        $data->success_url = $request->wallet_id? route('user.wallet.index') : route('user.deposit.history');
        $data->failed_url = route('user.deposit.history');
        $data->save();
        session()->put('Track', $data->trx);
        return to_route('user.deposit.confirm');
    }


    public function appDepositConfirm($hash) {
        try {
            $id = decrypt($hash);
        } catch (\Exception $ex) {
            abort(404);
        }
        $data = Deposit::where('id', $id)->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'DESC')->firstOrFail();

        // Only continue if the deposit owner is already authenticated — never auto-login from a hash.
        if (!auth()->check() || (int) auth()->id() !== (int) $data->user_id) {
            session()->put('Track', $data->trx);
            session()->put('pending_deposit_confirm', $data->trx);
            $notify[] = ['error', 'Please login to continue your deposit'];
            return to_route('user.login')->withNotify($notify);
        }

        session()->put('Track', $data->trx);
        return to_route('user.deposit.confirm');
    }


    public function depositConfirm() {
        $track = session()->get('Track');
        $deposit = Deposit::where('trx', $track)->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'DESC')->with('gateway')->firstOrFail();

        if ($deposit->method_code >= 1000) {
            return to_route('user.deposit.manual.confirm');
        }


        $dirName = $deposit->gateway->alias;
        $new = __NAMESPACE__ . '\\' . $dirName . '\\ProcessController';

        $data = $new::process($deposit);
        $data = json_decode($data);


        if (isset($data->error)) {
            $notify[] = ['error', $data->message];
            return back()->withNotify($notify);
        }
        if (isset($data->redirect)) {
            return redirect($data->redirect_url);
        }

        // for Stripe V3
        if(isset($data->session)){
            $deposit->btc_wallet = $data->session->id;
            $deposit->save();
        }

        $pageTitle = 'Payment Confirm';
        return view("Template::$data->view", compact('data', 'pageTitle', 'deposit'));
    }


    public static function userDataUpdate($deposit, $isManual = null) {
        \Illuminate\Support\Facades\DB::transaction(function () use ($deposit, $isManual) {
            $deposit = Deposit::where('id', $deposit->id)->lockForUpdate()->first();

            if (!$deposit || !in_array($deposit->status, [Status::PAYMENT_INITIATE, Status::PAYMENT_PENDING], true)) {
                return;
            }

            $deposit->status = Status::PAYMENT_SUCCESS;
            $deposit->save();

            $user = User::where('id', $deposit->user_id)->lockForUpdate()->first();
            if (!$user) {
                return;
            }

            $wallet = null;
            if ($deposit->wallet_id) {
                $wallet = Wallet::where('id', $deposit->wallet_id)->lockForUpdate()->first();
                $walletAmount = $deposit->amount * $wallet->currency->currency_rate;
                $wallet->balance += $walletAmount;
                $wallet->save();

                $balance = $wallet->balance;
                $deposit->wallet_amount = $walletAmount;
                $deposit->save();
            } else {
                $user->balance += $deposit->amount;
                $user->save();

                $balance = $user->balance;
            }

            $methodName = $deposit->methodName();

            $transaction = new Transaction();
            $transaction->user_id = $deposit->user_id;
            $transaction->wallet_id = $deposit->wallet_id;
            $transaction->amount = $deposit->amount;
            $transaction->wallet_amount = $deposit->wallet_amount;
            $transaction->post_balance = $balance;
            $transaction->charge = $deposit->charge;
            $transaction->trx_type = '+';
            $transaction->details = 'Deposit Via ' . $methodName;
            $transaction->trx = $deposit->trx;
            $transaction->remark = 'deposit';
            $transaction->save();

            if (!$isManual) {
                $adminNotification = new AdminNotification();
                $adminNotification->user_id = $user->id;
                $adminNotification->title = 'Deposit successful via ' . $methodName;
                $adminNotification->click_url = urlPath('admin.deposit.successful');
                $adminNotification->save();
            }

            $notifyTemplate = $isManual ? 'DEPOSIT_APPROVE' : 'DEPOSIT_COMPLETE';
            $notifyData = [
                'method_name' => $methodName,
                'method_currency' => $deposit->method_currency,
                'method_amount' => showAmount($deposit->final_amount, currencyFormat: false),
                'amount' => showAmount($deposit->amount, currencyFormat: false),
                'charge' => showAmount($deposit->charge, currencyFormat: false),
                'rate' => showAmount($deposit->rate, currencyFormat: false),
                'trx' => $deposit->trx,
                'post_balance' => showAmount($balance, currencyFormat: false)
            ];
            if ($deposit->wallet_id && $wallet) {
                $notifyTemplate = $isManual ? 'WALLET_DEPOSIT_APPROVE' : 'WALLET_DEPOSIT_COMPLETE';
                $notifyData['receive_amount'] = $deposit->amount * $wallet->currency->currency_rate;
                $notifyData['receive_currency'] = $wallet->currency->currency;
            }
            notify($user, $notifyTemplate, $notifyData);

            if ($deposit->is_card_issue) {
                self::completeVirtualCardIssuing($deposit);
            }

            if ($deposit->card_id && $deposit->is_topup) {
                $virtualCard = VirtualCard::with('user')->find($deposit->card_id);
                VirtualCardLib::updateCardForTopup($virtualCard, $deposit->topup_detail->amount, $deposit, false);

                if ($deposit->is_topup) {
                    $deposit->success_url = route('user.vcard.details', encrypt($virtualCard->id));
                    $deposit->save();
                }
            }

            ReferralCommission::levelCommission($user, $deposit->amount, $deposit->trx);
            updateAccountLevel($user);
            updateRewardPoint(Status::DEPOSIT_REWARD, $user, $deposit->amount, 'Reward Points for deposit');
        });
    }

    public function manualDepositConfirm() {
        $track = session()->get('Track');
        $data = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        abort_if(!$data, 404);
        if ($data->method_code > 999) {
            $pageTitle = 'Confirm Deposit';
            $method = $data->gatewayCurrency();
            $gateway = $method->method;
            return view('Template::user.payment.manual', compact('data', 'pageTitle', 'method', 'gateway'));
        }
        abort(404);
    }

    public function manualDepositUpdate(Request $request) {
        $track = session()->get('Track');
        $data = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        abort_if(!$data, 404);
        $gatewayCurrency = $data->gatewayCurrency();
        $gateway = $gatewayCurrency->method;
        $formData = $gateway->form->form_data;

        $formProcessor = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $userData = $formProcessor->processFormData($request, $formData);


        $data->detail = $userData;
        $data->status = Status::PAYMENT_PENDING;
        $data->save();


        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $data->user->id;
        $adminNotification->title = 'Deposit request from ' . $data->user->username;
        $adminNotification->click_url = urlPath('admin.deposit.details', $data->id);
        $adminNotification->save();

        $notifyTemplate = 'DEPOSIT_REQUEST';
        $notifyData = [
            'method_name' => $data->gatewayCurrency()->name,
            'method_currency' => $data->method_currency,
            'method_amount' => showAmount($data->final_amount, currencyFormat: false),
            'amount' => showAmount($data->amount, currencyFormat: false),
            'charge' => showAmount($data->charge, currencyFormat: false),
            'rate' => showAmount($data->rate, currencyFormat: false),
            'trx' => $data->trx
        ];

        if($data->wallet_id) {
            $wallet = $data->wallet;
            $notifyTemplate = 'WALLET_DEPOSIT_REQUEST';
            $notifyData['receive_amount'] = $data->amount * $wallet->currency->currency_rate;
            $notifyData['receive_currency'] = $wallet->currency->currency;
        }
        notify($data->user, $notifyTemplate, $notifyData);

        $notify[] = ['success', 'You have deposit request has been taken'];
        return to_route('user.deposit.history')->withNotify($notify);
    }

    private static function completeVirtualCardIssuing($deposit) {
        $user = $deposit->user;

        try {
            $issuedVirtualCard = VirtualCardLib::issueCard($deposit->card_issue_details, $user);
        } catch (\Exception $e) {
            info($e->getMessage());
        }

        $cardAmount = $deposit->card_issue_details->amount;
        $cardFee = (gs('card_issue_fee') + ($cardAmount * gs('card_issue_percent_fee') / 100)) ;

        $virtualCard = VirtualCardLib::createVirtualCard($user->id, $issuedVirtualCard->id,$cardAmount);

        VirtualCardLib::issueAmountSubtract($user, $virtualCard, $deposit->amount);

        // udpate the deposit success url
        $deposit->success_url = route('user.vcard.details', encrypt($virtualCard->id));
        $deposit->save();

        VirtualCardLib::cardIssueCompleted($user, $virtualCard, $cardAmount);

        VirtualCardLib::issueFeeSubtract($user, $virtualCard, $cardFee* gs('currency_exchange_rate'));
    }
}
