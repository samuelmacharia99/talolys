<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletCurrency;
use Illuminate\Http\Request;

class WalletController extends Controller {
    public function index() {
        $pageTitle  = 'Wallet';
        $wallets    = Wallet::where('user_id', auth()->id())->get();
        $currencies = WalletCurrency::active()->whereNotIn('id', $wallets->pluck('currency_id')->toArray() ?? [])->get();
        return view('Template::user.wallet.index', compact('pageTitle', 'wallets', 'currencies'));
    }

    public function store(Request $request) {
        $request->validate([
            'currency_id' => 'required|integer|exists:wallet_currencies,id|unique:wallets,currency_id,user_id',
            'name'        => 'required|string|max:255',
        ], [
            'currency_id.unique' => 'A wallet already exists for this currency.',
        ], []);

        $wallet              = new Wallet();
        $wallet->user_id     = auth()->id();
        $wallet->name        = $request->name;
        $wallet->currency_id = $request->currency_id;
        $wallet->balance     = 0;
        $wallet->save();

        $notify[] = ['success', 'Wallet created successfully'];
        return back()->withNotify($notify);
    }
}
