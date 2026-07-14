<?php

namespace App\Http\Controllers\BranchStaff;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\AdminNotification;
use App\Models\Branch;
use App\Models\Form;
use App\Models\User;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{

    public function all()
    {
        $staff     = authStaff();
        $accounts  = User::query();
        $branchId  = session('branchId');
        $branches  = $staff->designation == Status::ROLE_MANAGER ? $staff->assignBranch : null;

        if ($staff->designation == Status::ROLE_MANAGER) {
            $branch = Branch::active()->findOrFail($branchId);
            $pageTitle = 'All Accounts Opened from ' . $branch->name;
        } else {
            $accounts = $accounts->where('branch_staff_id', $staff->id);
            $pageTitle = 'All Accounts Opened By ' . $staff->name;
        }

        if ($branchId) {
            $accounts = $accounts->where('branch_id', $branchId);
        }

        $accounts = $accounts
            ->searchable(['username', 'email', 'firstname', 'lastname'])
            ->with('branch:id,name', 'branchStaff:id,name')
            ->latest()
            ->paginate(getPaginate());

        return view('branch_staff.user.list', compact('pageTitle', 'accounts', 'staff', 'branches', 'branchId'));
    }

    public function find()
    {
        return $this->detail(request()->account_number);
    }

    public function detail($accountNumber)
    {
        $staff   = authStaff();
        $account = $accountNumber;
        $user    = User::where('username', $account)->orWhere('account_number', $account)->first();

        if (!$user) {
            $notify[] = ['error', 'Account not found'];
            return back()->withNotify($notify)->withInput();
        }

        $pageTitle = 'Account Details';
        return view('branch_staff.user.detail', compact('pageTitle', 'user', 'staff'));
    }

    public function open($account = null)
    {

        $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        if ($account) {
            $account   = User::where('account_number', $account)->firstOrFail();
            $action    = route('staff.account.update', @$account->id);
            $pageTitle = 'Edit Account Details';
        } else {
            $pageTitle = 'Open New Account';
            $action    = route('staff.account.save');
        }

        return view('branch_staff.user.form', compact('pageTitle', 'countries', 'account', 'action'));
    }

    public function store(Request $request)
    {
        $this->validation($request);

        $staff  = authStaff();
        $branch = $this->resolveBranch($staff);

        if (!$branch) {
            $notify[] = ['error', 'No branch is assigned to this staff account. Contact an administrator.'];
            return back()->withNotify($notify)->withInput();
        }

        $kycData = null;
        if (gs('kv')) {
            $form = Form::where('act', 'kyc')->first();
            if (!$form) {
                $notify[] = ['error', 'KYC form is not configured yet'];
                return back()->withNotify($notify)->withInput();
            }
            $formData          = $form->form_data;
            $formProcessor     = new FormProcessor();
            $kycValidationRule = $formProcessor->valueValidation($formData);
            $request->validate($kycValidationRule);

            try {
                $kycData = $formProcessor->processFormData($request, $formData);
            } catch (\Throwable $e) {
                $notify[] = ['error', 'Couldn\'t upload KYC documents. Please try again.'];
                return back()->withNotify($notify)->withInput();
            }
        }

        $password = getTrx(8);
        $user     = new User();

        if (@gs('modules')->referral_system && $request->referrer) {
            $referrer = User::where('account_number', $request->referrer)->first();

            if (!$referrer) {
                $notify[] = ['error', 'Referrer account not found'];
                return back()->withNotify($notify)->withInput();
            }

            $user->ref_by                    = $referrer->id;
            $user->referral_commission_count = gs('referral_commission_count') ?? 0;
        }

        $user->password         = Hash::make($password);
        $user->kyc_data         = $kycData;
        $user->branch_id        = $branch->id;
        $user->branch_staff_id  = $staff->id;
        $user->account_number   = generateAccountNumber();
        $user->kv               = gs('kv') ? Status::KYC_PENDING : Status::KYC_VERIFIED;
        $user->ev               = gs('ev') ? Status::NO : Status::YES;
        $user->sv               = gs('sv') ? Status::NO : Status::YES;
        $user->status           = Status::USER_ACTIVE;
        $user->ts               = Status::DISABLE;
        $user->tv               = Status::VERIFIED;
        $user->profile_complete = Status::YES;

        try {
            $user = $this->saveUser($request, $user);
        } catch (\Throwable $e) {
            report($e);
            $notify[] = ['error', $e->getMessage() ?: 'Couldn\'t create the account. Please try again.'];
            return back()->withNotify($notify)->withInput();
        }

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $user->id;
        $adminNotification->title     = 'New account opened from ' . $branch->name;
        $adminNotification->click_url = urlPath('admin.users.detail', $user->id);
        $adminNotification->save();

        try {
            notify($user, 'ACCOUNT_OPENED', [
                'email'    => $user->email,
                'username' => $user->username,
                'password' => $password,
            ]);
        } catch (\Throwable $e) {
            report($e);
        }

        $notify[] = ['success', 'Account opened successfully. Username: ' . $user->username . ', Password: ' . $password];
        return back()->withNotify($notify);
    }

    public function update(Request $request, $id)
    {
        $user      = User::where('branch_staff_id', authStaff()->id)->findOrFail($id);
        $oldEmail  = $user->email;
        $oldMobile = $user->mobile;
        $this->validation($request, $id);

        try {
            $user = $this->saveUser($request, $user);
        } catch (\Throwable $e) {
            report($e);
            $notify[] = ['error', $e->getMessage() ?: 'Couldn\'t update the account. Please try again.'];
            return back()->withNotify($notify)->withInput();
        }

        if ($oldEmail != $user->email) {
            $user->ev = 0;
            $user->save();
        }

        if ($oldMobile != $user->mobile) {
            $user->sv = 0;
            $user->save();
        }

        $notify[] = ['success', 'Account information updated successfully'];
        return back()->withNotify($notify);
    }

    protected function saveUser($request, $user)
    {
        $countryData = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryCode = $request->country;
        $country     = $countryData->$countryCode ?? null;

        if (!$country) {
            throw ValidationException::withMessages(['country' => 'Invalid country selected']);
        }

        $dialCode = $request->mobile_code ?: $country->dial_code;

        $user->firstname    = $request->firstname;
        $user->lastname     = $request->lastname;
        $user->email        = strtolower(trim($request->email));
        $user->username     = trim($request->username);
        $user->country_code = $countryCode;
        $user->mobile       = $request->mobile;
        $user->dial_code    = $dialCode;
        $user->address      = $request->address;
        $user->city         = $request->city;
        $user->state        = $request->state;
        $user->zip          = $request->zip;
        $user->country_name = $country->country;

        if ($request->hasFile('image')) {
            try {
                $user->image = fileUploader(
                    $request->image,
                    getFilePath('userProfile'),
                    getFileSize('userProfile'),
                    $user->image
                );
            } catch (\Throwable $e) {
                throw new \RuntimeException('Couldn\'t upload the profile image');
            }
        }

        $user->save();
        return $user;
    }

    private function resolveBranch($staff)
    {
        if (session('branchId')) {
            $branch = Branch::active()->find(session('branchId'));
            if ($branch) {
                return $branch;
            }
        }

        return $staff->branch();
    }

    private function validation($request, $id = 0)
    {
        $countryData  = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryArray = (array) $countryData;
        $countries    = implode(',', array_keys($countryArray));
        $imgValidation = $id ? 'nullable' : 'required';

        $request->validate([
            'firstname'   => 'required|string',
            'lastname'    => 'required|string',
            'email'       => 'required|string|email|unique:users,email,' . $id,
            'mobile'      => 'required|regex:/^([0-9]*)$/',
            'mobile_code' => 'nullable|string',
            'username'    => 'required|min:6|unique:users,username,' . $id,
            'country'     => 'required|in:' . $countries,
            'image'       => [$imgValidation, new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'referrer'    => 'nullable|string',
            'address'     => 'required|string',
            'city'        => 'required|string',
            'state'       => 'required|string',
            'zip'         => 'required|string',
        ]);

        if (preg_match('/[^a-z0-9_]/', trim($request->username))) {
            throw ValidationException::withMessages([
                'username' => 'Username can contain only small letters, numbers and underscore. No special character, space or capital letters.',
            ]);
        }

        $dialCode = $request->mobile_code;
        if (!$dialCode && isset($countryData->{$request->country})) {
            $dialCode = $countryData->{$request->country}->dial_code;
        }

        $exists = User::where('mobile', $request->mobile)
            ->where('dial_code', $dialCode)
            ->when($id, fn ($q) => $q->where('id', '!=', $id))
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'mobile' => 'The mobile number already exists',
            ]);
        }
    }
}
