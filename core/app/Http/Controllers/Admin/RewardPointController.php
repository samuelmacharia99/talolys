<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\AccountLevel;
use App\Models\RewardPointEarning;
use App\Models\RewardPointRedeem;
use Illuminate\Http\Request;

class RewardPointController extends Controller {
    public function earningList() {
        $pageTitle = 'Reward Point Earnings';
        $rewards   = RewardPointEarning::searchable(['name', 'accountLevel:name'])
            ->filterable()
            ->orderable()
            ->dynamicPaginate();
        return view('admin.reward_point.earning_list', compact('pageTitle', 'rewards'));
    }

    public function earningCreate() {
        $pageTitle     = "New Reward Point Earning";
        $accountLevels = AccountLevel::active()->orderBy('name')->get();
        return view('admin.reward_point.earning_create', compact('pageTitle', 'accountLevels'));
    }

    public function earningEdit($id) {
        $pageTitle     = "Edit Reward Point Earning";
        $reward        = RewardPointEarning::findOrFail($id);
        $accountLevels = AccountLevel::active()->orderBy('name')->get();
        return view('admin.reward_point.earning_create', compact('pageTitle', 'accountLevels', 'reward'));
    }

    public function earningStore(Request $request, $id = 0) {
        $request->validate([
            'name'               => 'required|string:max:255',
            'account_level_id'   => 'nullable|exists:account_levels,id',
            'transaction_amount' => 'required|numeric|gt:0',
            'reward_point'       => 'required|numeric|gt:0',
            'max_use'            => 'required|integer',
            'per_user_limit'     => 'required|integer',
            'started_at'         => 'nullable|date',
            'expired_at'         => 'nullable|date',
            'reward_type'        => 'required|array',
            'reward_type.*'      => 'required|integer|in:' . implode(',', array_keys(rewardTypes())),
        ]);

        if ($id) {
            $rewardPoint = RewardPointEarning::findOrFail($id);
            $message     = 'Reward point earning updated successfully';
        } else {
            $rewardPoint = new RewardPointEarning();
            $message     = 'Reward point earning added successfully';
        }

        $rewardPoint->name               = $request->name;
        $rewardPoint->account_level_id   = $request->account_level_id;
        $rewardPoint->transaction_amount = $request->transaction_amount;
        $rewardPoint->reward_point       = $request->reward_point;
        $rewardPoint->max_use            = $request->max_use;
        $rewardPoint->per_user_limit     = $request->per_user_limit;
        $rewardPoint->started_at         = $request->started_at;
        $rewardPoint->expired_at         = $request->expired_at;
        $rewardPoint->reward_type        = $request->reward_type;
        $rewardPoint->save();

        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    public function earningStatus($id) {
        return RewardPointEarning::changeStatus($id);
    }

    public function redeemList() {
        $pageTitle = 'Reward Point Redeems';
        $redeems   = RewardPointRedeem::searchable(['accountLevel:name'])
            ->filterable()
            ->orderable()
            ->dynamicPaginate();
        $accountLevels = AccountLevel::active()->orderBy('name')->get();
        return view('admin.reward_point.redeem_list', compact('pageTitle', 'redeems', 'accountLevels'));
    }

    public function redeemStore(Request $request, $id = 0) {
        $request->validate([
            'name'             => 'required|string:max:255',
            'account_level_id' => 'nullable|exists:account_levels,id',
            'redeem_point'     => 'required|numeric|gt:0',
            'redeem_amount'    => 'required|numeric|gt:0',
        ]);

        if ($id) {
            $rewardPoint = RewardPointRedeem::findOrFail($id);
            $message     = 'Reward point redeem updated successfully';
        } else {
            $rewardPoint = new RewardPointRedeem();
            $message     = 'Reward point redeem added successfully';
        }

        $rewardPoint->name             = $request->name;
        $rewardPoint->account_level_id = $request->account_level_id;
        $rewardPoint->redeem_point     = $request->redeem_point;
        $rewardPoint->redeem_amount    = $request->redeem_amount;
        $rewardPoint->save();

        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    public function redeemStatus($id) {
        return RewardPointRedeem::changeStatus($id);
    }
}
