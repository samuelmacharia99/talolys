<?php

namespace App\Http\Controllers\Admin;

use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\WalletCurrency;

class WalletController extends Controller
{
    public function list()
    {
        $pageTitle = 'Wallet';
        $wallets = Wallet::searchable(['user:username', 'name'])
            ->filterable()
            ->orderable()
            ->dynamicPaginate();
        return view('admin.wallet.list', compact('pageTitle', 'wallets'));
    }

    public function currency()
    {
        $pageTitle = 'Currency';
        $currencies = WalletCurrency::searchable(['currency'])
            ->filterable()
            ->orderable()
            ->dynamicPaginate();
        return view('admin.wallet.currency', compact('pageTitle', 'currencies'));
    }


    public function currencyStore(Request $request, $id=0)
    {
        $request->validate([
            'currency' => 'required|unique:wallet_currencies,currency,'.$id.'|string:max:40',
            'symbol' => 'required|unique:wallet_currencies,symbol,'.$id.'|max:40',
            'currency_rate' => 'required|numeric|gt:0'
        ]);

        if($id) {
            $currency = WalletCurrency::findOrFail($id);
            $message = 'Currency updated successfully';
        } else {
            $currency = new WalletCurrency();
            $message = 'Currency added successfully';
        }

        $currency->currency = $request->currency;
        $currency->symbol = $request->symbol;
        $currency->currency_rate = $request->currency_rate;
        $currency->save();

        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    public function currencyStatus($id)
    {
        return WalletCurrency::changeStatus($id);
    }


    public function currencyApiUpdate(Request $request)
    {
        $request->validate([
            'api_key' => 'required|string',
        ]);

        $general                   = gs();
        $general->currency_api_key = $request->api_key;
        $general->save();

        $notify[] = ['success', 'Api key updated successfully'];
        return back()->withNotify($notify);
    }

    public function getCurrencyRate(Request $request) {
        return currencyRate($request->currency);
    }

}
