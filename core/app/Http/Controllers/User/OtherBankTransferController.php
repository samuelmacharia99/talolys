<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\OTPManager;
use App\Models\AdminNotification;
use App\Models\BalanceTransfer;
use App\Models\Beneficiary;
use App\Models\OtherBank;
use App\Models\OtpVerification;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use stdClass;

class OtherBankTransferController extends Controller {
    public function beneficiaries() {
        $pageTitle     = 'Transfer Money to Other Bank';
        $beneficiaries = Beneficiary::where('user_id', auth()->id())
            ->where('beneficiary_type', OtherBank::class)
            ->with('beneficiaryOf')
            ->paginate(getPaginate());
        $wallets = Wallet::where('user_id', auth()->id())->get();
        return view('Template::user.transfer.other_bank.beneficiaries', compact('pageTitle', 'beneficiaries', 'wallets'));
    }

    public function transferRequest(Request $request, $id) {
        $beneficiary = Beneficiary::where('user_id', auth()->id())->with('beneficiaryOf')->findOrFail($id);

        $this->validation($request, $beneficiary);

        $wallet = null;
        if ($request->wallet_id) {
            $wallet = Wallet::where('user_id', auth()->id())->findOrFail($request->wallet_id);
            $this->checkBankAllowCurrency($wallet->currency->currency, $beneficiary?->beneficiaryOf?->supported_currency ?? null);
        }

        $this->checkTransferAvailability($request->amount, $beneficiary->beneficiaryOf, $wallet);

        $walletObject = new stdClass;
        if ($wallet) {
            $walletObject->id       = $wallet->id;
            $walletObject->name     = $wallet->name;
            $walletObject->currency = $wallet->currency?->currency;
            $walletObject->symbol   = $wallet->currency?->symbol;
        }

        $additionalData = [
            'amount'         => $request->amount,
            'wallet'         => $walletObject,
            'after_verified' => 'user.transfer.other.bank.confirm',
        ];

        $otpManager = new OTPManager();
        return $otpManager->newOTP($beneficiary, $request->auth_mode, 'OTHER_BANK_TRANSFER_OTP', $additionalData);
    }

    public function confirm() {
        return \Illuminate\Support\Facades\DB::transaction(function () {
            $verification = OtpVerification::find(sessionVerificationId());
            $beneficiary  = $verification->verifiable;
            $bank         = $beneficiary->beneficiaryOf;

            OTPManager::checkVerificationData($verification, Beneficiary::class);
            OTPManager::markActionCompleted($verification);

            if ($beneficiary->beneficiary_type != OtherBank::class) {
                $notify[] = ['error', 'Invalid session data'];
                return to_route('user.home')->withNotify($notify);
            }

            $sender = \App\Models\User::where('id', auth()->id())->lockForUpdate()->first();
            $amount = $verification->additional_data->amount;
            $wallet = $verification->wallet ?? null;
            if ($wallet) {
                $wallet = Wallet::where('id', $wallet->id)->lockForUpdate()->first();
            }
            $rate   = $wallet ? $wallet->currency->currency_rate : 1;

            if ($wallet) {
                $this->checkBankAllowCurrency($wallet->currency->currency, $beneficiary?->beneficiaryOf?->supported_currency ?? null);
            }

            $this->checkTransferAvailability($amount, $bank, $wallet);

            $charge      = $this->charge($amount, $bank, $wallet);
            $finalAmount = $amount + $charge;

            $transfer                       = new BalanceTransfer();
            $transfer->user_id              = $sender->id;
            $transfer->wallet_id            = $wallet ? $wallet->id : 0;
            $transfer->trx                  = getTrx();
            $transfer->beneficiary_id       = $beneficiary->id;
            $transfer->amount               = $amount;
            $transfer->base_currency_amount = $amount / $rate;
            $transfer->charge               = $charge;
            $transfer->status               = Status::TRANSFER_PENDING;
            $transfer->save();

            if ($wallet) {
                $wallet->balance -= $finalAmount;
                $wallet->save();
            } else {
                $sender->balance -= $finalAmount;
                $sender->save();
            }

            $this->sendingTransaction($transfer, $sender, $wallet);

            $adminNotification            = new AdminNotification();
            $adminNotification->user_id   = $sender->id;
            $adminNotification->title     = 'New bank transfer request';
            $adminNotification->click_url = urlPath('admin.transfers.details', $transfer->id);
            $adminNotification->save();

            session()->forget('otp_id');

            $shortCodes = $this->shortCodes($transfer, $sender, $bank, $wallet);
            notify($sender, $wallet ? 'WALLET_OTHER_BANK_TRANSFER_REQUEST_SEND' : 'OTHER_BANK_TRANSFER_REQUEST_SEND', $shortCodes);

            $notify[] = ['success', "Request submitted successfully"];
            return to_route('user.transfer.details', $transfer->trx)->withNotify($notify);
        });
    }

    private function checkTransferAvailability($amount, $bank, $wallet = null) {
        if ($bank->status == 0) {
            throw ValidationException::withMessages(['error' => 'Sorry! transfers to ' . $bank->name . ' are currently unavailable.']);
        }

        $user        = auth()->user();
        $balance     = $wallet ? $wallet->balance : $user->balance;
        $finalAmount = $amount + $this->charge($amount, $bank, $wallet);
        $rate        = $wallet ? $wallet->currency?->currency_rate : 1;

        if ($balance < $finalAmount) {
            throw ValidationException::withMessages(['error' => 'Sorry! You don\'t have sufficient balance']);
        }

        $minimumTransferLimit = $bank->minimum_limit * $rate;
        if ($amount < $minimumTransferLimit) {
            throw ValidationException::withMessages(['error' => 'Sorry minimum transfer limit is ' . showAmount($minimumTransferLimit, walletCurrency: ($wallet ? $wallet->currency : null))]);
        }

        $maxTransferLimit = $bank->maximum_limit * $rate;
        if ($amount > $maxTransferLimit) {
            throw ValidationException::withMessages(['error' => 'Sorry maximum transfer limit is ' . showAmount($maxTransferLimit, walletCurrency: ($wallet ? $wallet->currency : null))]);
        }

        // Daily and monthly limits by amount
        $todaysTotal = BalanceTransfer::otherBank()->notRejected()->where('user_id', $user->id)->whereDate('created_at', now())->sum('base_currency_amount');
        if (($todaysTotal + ($amount / $rate)) > $bank->daily_maximum_limit) {
            throw ValidationException::withMessages(['error' => 'Sorry you are exceeding the daily transfer limit']);
        }

        $thisMonthTotal = BalanceTransfer::otherBank()->notRejected()->where('user_id', $user->id)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('base_currency_amount');
        if (($thisMonthTotal + ($amount / $rate)) > $bank->monthly_maximum_limit) {
            throw ValidationException::withMessages(['error' => 'Sorry you are exceeding the monthly transfer limit']);
        }
    }

    private function checkBankAllowCurrency($currency, $supportedCurrency = []) {
        $hasCurrency = false;
        if ($supportedCurrency) {
            $hasCurrency = in_array($currency, $supportedCurrency);
        }
        if (!$supportedCurrency || !$hasCurrency) {
            throw ValidationException::withMessages(['error' => "The beneficiary does not allow the {$currency}."]);
        }
    }

    private function charge($amount, $bank, $wallet = null) {
        $rate          = $wallet ? $wallet->currency->currency_rate : 1;
        $percentCharge = ($amount * $bank->percent_charge) / 100;
        return ($bank->fixed_charge * $rate) + $percentCharge;
    }

    private function sendingTransaction($transfer, $user, $wallet = null) {
        $transaction               = new Transaction();
        $transaction->user_id      = $user->id;
        $transaction->wallet_id    = $wallet ? $wallet->id : 0;
        $transaction->amount       = $transfer->amount + $transfer->charge;
        $transaction->post_balance = $wallet ? $wallet->balance : $user->balance;
        $transaction->charge       = $transfer->charge;
        $transaction->trx_type     = '-';
        $transaction->details      = 'Other bank transfer';
        $transaction->remark       = 'other_bank_transfer';
        $transaction->trx          = $transfer->trx;
        $transaction->save();
    }

    private function validation($request, $beneficiary) {
        if ($beneficiary->beneficiary_type != OtherBank::class) {
            throw ValidationException::withMessages(['error' => 'Invalid beneficiary selected']);
        }

        $rules = [
            'amount'    => "required|numeric|gt:0",
            'wallet_id' => 'nullable|numeric|exists:wallets,id',
        ];
        $rules = mergeOtpField($rules);
        $request->validate($rules);
    }

    private function shortCodes($transfer, $sender, $bank, $wallet = null) {
        $data = [
            "sender_account_number"    => $sender->account_number,
            "sender_account_name"      => $sender->username,
            "recipient_account_number" => $transfer->beneficiary->account_number,
            "recipient_account_name"   => $transfer->beneficiary->account_name,
            "sending_amount"           => showAmount($transfer->amount, currencyFormat: false),
            "charge"                   => showAmount($transfer->charge, currencyFormat: false),
            "final_amount"             => showAmount($transfer->amount + $transfer->charge, currencyFormat: false),
            "bank_name"                => $bank->name,
            'post_balance'             => showAmount(($wallet ? $wallet->balance : $sender->balance), currencyFormat: false),
        ];

        if ($wallet) {
            $walletCurrency                 = $wallet->currency;
            $data['currency_rate']          = $walletCurrency->currency_rate;
            $data['wallet_currency']        = $walletCurrency->currency;
            $data['wallet_currency_symbol'] = $walletCurrency->symbol;
        }

        return $data;
    }
}
