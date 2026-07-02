<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\OTPManager;
use App\Models\BalanceTransfer;
use App\Models\Beneficiary;
use App\Models\OtpVerification;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use stdClass;

class OwnBankTransferController extends Controller {

    public function beneficiaries() {
        $pageTitle     = 'Transfer Money Within ' . gs('site_name');
        $beneficiaries = Beneficiary::where('user_id', auth()->id())->where('beneficiary_type', User::class)->paginate(getPaginate());
        $wallets       = Wallet::where('user_id', auth()->id())->get();
        return view('Template::user.transfer.own_bank.beneficiaries', compact('pageTitle', 'beneficiaries', 'wallets'));
    }

    public function transferRequest(Request $request, $id) {
        $beneficiary = Beneficiary::where('user_id', auth()->id())->findOrFail($id);
        $this->validation($request, $beneficiary);
        $wallet = null;
        if ($request->wallet_id) {
            $wallet = Wallet::where('user_id', auth()->id())->findOrFail($request->wallet_id);
            $this->checkReceiverWallet($wallet, $beneficiary?->beneficiaryOf?->wallets ?? null);
        }
        $this->checkTransferAvailability($request->amount, $wallet);

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
            'after_verified' => 'user.transfer.own.bank.confirm',
        ];

        $otpManager = new OTPManager();
        return $otpManager->newOTP($beneficiary, $request->auth_mode, 'OWN_BANK_TRANSFER_OTP', $additionalData);
    }

    public function confirm() {
        $verification    = OtpVerification::find(sessionVerificationId());
        $beneficiary     = $verification->verifiable;
        $recipient       = $beneficiary->beneficiaryOf;
        $recipientWallet = null;

        OTPManager::checkVerificationData($verification, Beneficiary::class);

        if ($beneficiary->beneficiary_type != User::class) {
            $notify[] = ['error', 'Invalid session data'];
            return to_route('user.home')->withNotify($notify);
        }

        $sender = auth()->user();
        $amount = $verification->additional_data->amount;
        $wallet = $verification->wallet ?? null;
        $rate   = $wallet ? $wallet->currency->currency_rate : 1;
        if ($wallet) {
            $this->checkReceiverWallet($wallet, $beneficiary?->beneficiaryOf?->wallets ?? null);
            $recipientWallet = $recipient->wallets()->where('currency_id', $wallet->currency_id)->first();
        }

        $this->checkTransferAvailability($amount, $wallet);

        $charge      = $this->charge($amount, $wallet);
        $finalAmount = $amount + $charge;

        $transfer                       = new BalanceTransfer();
        $transfer->user_id              = $sender->id;
        $transfer->wallet_id            = $wallet ? $wallet->id : 0;
        $transfer->trx                  = getTrx();
        $transfer->beneficiary_id       = $beneficiary->id;
        $transfer->amount               = $amount;
        $transfer->base_currency_amount = $amount / $rate;
        $transfer->charge               = $charge;
        $transfer->status               = Status::TRANSFER_COMPLETED;
        $transfer->save();

        if ($wallet) {
            $wallet->balance -= $finalAmount;
            $wallet->save();
        } else {
            $sender->balance -= $finalAmount;
            $sender->save();
        }

        $this->sendingTransaction($transfer, $sender, $wallet); // Insert Sending Transaction

        if ($recipientWallet) {
            $recipientWallet->balance += $amount;
            $recipientWallet->save();
        } else {
            $recipient->balance += $transfer->amount;
            $recipient->save();
        }

        $this->receivingTransaction($transfer, $recipient, $recipientWallet); // Insert Receiving Transaction

        $shortCodes = $this->shortCodes($transfer, $sender, $recipient, $sender->balance);
        notify($sender, $wallet? 'WALLET_OWN_BANK_TRANSFER_MONEY_SEND' : 'OWN_BANK_TRANSFER_MONEY_SEND', $shortCodes);

        $shortCodes = $this->shortCodes($transfer, $sender, $recipient, $recipient->balance);
        notify($recipient, $wallet? 'WALLET_OWN_BANK_TRANSFER_MONEY_RECEIVE' : 'OWN_BANK_TRANSFER_MONEY_RECEIVE', $shortCodes);


        session()->forget('otp_id');
        updateRewardPoint(Status::OWN_BANK_TRANSFER_REWARD, $sender, $transfer->base_currency_amount, 'Reward Points for Own Bank Transfer');

        $notify[] = ['success', showAmount($transfer->amount, walletCurrency: ($wallet ? $wallet->currency : null)) ." transferred successfully"];
        return to_route('user.transfer.details', $transfer->trx)->withNotify($notify);
    }

    private function sendingTransaction($transfer, $user, $wallet = null) {
        $transaction               = new Transaction();
        $transaction->user_id      = $user->id;
        $transaction->wallet_id    = $transfer->wallet_id;
        $transaction->amount       = $transfer->final_amount;
        $transaction->post_balance = $wallet ? $wallet->balance : $user->balance;
        $transaction->charge       = $transfer->charge;
        $transaction->trx_type     = '-';
        $transaction->details      = 'Own bank transfer';
        $transaction->trx          = $transfer->trx;
        $transaction->remark       = "own_bank_transfer";
        $transaction->save();
    }

    private function receivingTransaction($transfer, $user, $wallet = null) {
        $transaction               = new Transaction();
        $transaction->user_id      = $user->id;
        $transaction->wallet_id    = $wallet ? $wallet->id :  0;
        $transaction->amount       = $transfer->amount;
        $transaction->post_balance = $wallet ? $wallet->balance : $user->balance;
        $transaction->charge       = 0;
        $transaction->trx_type     = '+';
        $transaction->details      = 'Received transferred money';
        $transaction->remark       = 'received_money';
        $transaction->trx          = $transfer->trx;
        $transaction->save();
    }

    private function checkReceiverWallet($wallet, $beneficiaryWallets = null) {
        $hasWallet = false;
        if ($beneficiaryWallets) {
            $hasWallet = $beneficiaryWallets->where('currency_id', $wallet->currency_id)->first() ?? false;
        }
        if (!$beneficiaryWallets || !$hasWallet) {
            throw ValidationException::withMessages(['error' => "The beneficiary does not have a {$wallet->currency?->currency} wallet."]);
        }
    }

    private function checkTransferAvailability($amount, $wallet = null) {
        $rate    = 1;
        $user    = auth()->user();
        $balance = $user->balance;
        if ($wallet) {
            $rate    = $wallet->currency->currency_rate;
            $balance = $wallet->balance;
        }
        $finalAmount          = $amount + $this->charge($amount, $wallet);
        $minimumTransferLimit = gs('minimum_transfer_limit') * $rate;

        if ($amount < $minimumTransferLimit) {
            throw ValidationException::withMessages(['error' => 'Sorry minimum transfer limit is ' . showAmount($minimumTransferLimit, walletCurrency: ($wallet ? $wallet->currency : null))]);
        }

        if ($balance < $finalAmount) {
            throw ValidationException::withMessages(['error' => 'Sorry! You don\'t have sufficient balance']);
        }

        $todaysTotal = BalanceTransfer::completed()->where('user_id', $user->id)->ownBank()->whereDate('created_at', now())->sum('base_currency_amount');

        if (($todaysTotal + ($amount / $rate)) > gs('daily_transfer_limit')) {
            throw ValidationException::withMessages(['error' => 'Sorry you are exceeding the daily transfer limit']);
        }

        $thisMonthTotal = BalanceTransfer::completed()->where('user_id', $user->id)->ownBank()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('base_currency_amount');

        if (($thisMonthTotal + ($amount / $rate)) > gs('monthly_transfer_limit')) {
            throw ValidationException::withMessages(['error' => 'Sorry you are exceeding the monthly transfer limit']);
        }
    }

    private function charge($amount, $wallet = null) {
        $rate = 1;
        if ($wallet) {
            $rate = $wallet->currency->currency_rate;
        }
        $percentCharge = ($amount * gs('percent_transfer_charge')) / 100;
        return (gs('fixed_transfer_charge') * $rate) + $percentCharge;
    }

    private function validation($request, $beneficiary) {
        if ($beneficiary->beneficiary_type != User::class) {
            throw ValidationException::withMessages(['error' => 'Invalid beneficiary selected']);
        }

        $rules = [
            'amount'    => "required|numeric|gt:0",
            'wallet_id' => 'nullable|numeric|exists:wallets,id',
        ];
        $rules = mergeOtpField($rules);
        $request->validate($rules);
    }

    private function shortCodes($transfer, $sender, $recipient, $postBalance) {
        $data = [
            'sender'       => $sender->username,
            'recipient'    => $recipient->username,
            'amount'       => showAmount($transfer->amount, currencyFormat: false),
            'charge'       => showAmount($transfer->charge, currencyFormat: false),
            'final_amount' => showAmount(($transfer->amount + $transfer->charge), currencyFormat: false),
            'trx'          => $transfer->trx,
            'post_balance' => showAmount($postBalance, currencyFormat: false),
        ];
        if($transfer->wallet_id) {
            $walletCurrency = $transfer->wallet->currency;
            $data['currency_rate'] = $walletCurrency->currency_rate;
            $data['wallet_currency'] = $walletCurrency->currency;
            $data['wallet_currency_symbol']   = $walletCurrency->symbol;
            $data['post_balance'] = showAmount($transfer->wallet->balance, currencyFormat: false);
        }
        return $data;
    }
}
