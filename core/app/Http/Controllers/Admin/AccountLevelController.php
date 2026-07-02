<?php

namespace App\Http\Controllers\Admin;

use App\Models\AccountLevel;
use Illuminate\Http\Request;
use App\Rules\FileTypeValidate;
use App\Http\Controllers\Controller;

class AccountLevelController extends Controller
{
    public function list() {
        $pageTitle = "Account Level";
        $levels = AccountLevel::searchable(['name'])
            ->filterable()
            ->orderable()
            ->dynamicPaginate();
        return view('admin.account_level.list', compact('pageTitle', 'levels'));
    }

    public function store(Request $request, $id=0)
    {
          $request->validate([
            'name' => 'required|unique:account_levels,name,'.$id.'|string:max:40',
            'icon' => [$id ? 'nullable' : 'required', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'min_transaction_amount' => 'required|numeric|gte:0',
            'bonus_amount' => 'nullable|numeric|gte:0',
        ]);

        if($id) {
            $level = AccountLevel::findOrFail($id);
            $message = 'Account level updated successfully';
        } else {
            $level = new AccountLevel();
            $message = 'Account level added successfully';
        }
        if ($request->hasFile('icon')) {
            try {
                $level->icon = fileUploader($request->icon, getFilePath('accountLevel'), getFileSize('accountLevel'), ($level->icon ?? NULL));
            } catch (\Exception $exp) {
                $notify[] = ['errors', 'Image could not be uploaded'];
                return back()->withNotify($notify);
            }
        }

        $level->name = $request->name;
        $level->min_transaction_amount = $request->min_transaction_amount;
        $level->bonus_amount = $request->bonus_amount;
        $level->save();

        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }


    public function status($id)
    {
        return AccountLevel::changeStatus($id);
    }
}
