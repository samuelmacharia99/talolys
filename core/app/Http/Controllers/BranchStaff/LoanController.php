<?php

namespace App\Http\Controllers\BranchStaff;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\AdminNotification;
use App\Models\Branch;
use App\Models\Loan;
use App\Models\LoanPlan;
use App\Models\User;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function list()
    {
        $staff = authStaff();
        $loans = Loan::query()->with(['user', 'plan', 'branchStaff:id,name', 'nextInstallment']);

        if ($staff->designation == Status::ROLE_ACCOUNT_OFFICER) {
            $pageTitle = 'Loans Originated by ' . $staff->name;
            $loans->where('branch_staff_id', $staff->id);
        } else {
            $branch = Branch::active()->findOrFail(session('branchId'));
            $pageTitle = 'Loans in ' . $branch->name . ' Branch';
            $loans->where('branch_id', $branch->id);
        }

        if (request()->status !== null && request()->status !== '') {
            $loans->where('status', request()->status);
        }

        $loans = $loans->searchable(['loan_number', 'user:account_number,username,firstname,lastname', 'plan:name'])
            ->dateFilter()
            ->latest()
            ->paginate(getPaginate());

        return view('branch_staff.loan.list', compact('pageTitle', 'loans', 'staff'));
    }

    public function plans($accountNumber)
    {
        $user = $this->findEligibleCustomer($accountNumber);
        if (!$user instanceof User) {
            return $user;
        }

        $pageTitle = 'Apply Loan for ' . $user->fullname;
        $plans     = LoanPlan::active()->latest()->get();

        return view('branch_staff.loan.plans', compact('pageTitle', 'plans', 'user'));
    }

    public function applyAmount(Request $request, $accountNumber, $planId)
    {
        $user = $this->findEligibleCustomer($accountNumber);
        if (!$user instanceof User) {
            return $user;
        }

        $plan = LoanPlan::active()->findOrFail($planId);
        $request->validate([
            'amount' => "required|numeric|min:{$plan->minimum_amount}|max:{$plan->maximum_amount}",
        ]);

        session()->put('staff_loan', [
            'user_id'         => $user->id,
            'account_number'  => $user->account_number,
            'plan_id'         => $plan->id,
            'amount'          => $request->amount,
        ]);

        return to_route('staff.loan.apply.form');
    }

    public function applyForm()
    {
        $sessionLoan = session('staff_loan');
        if (!$sessionLoan) {
            $notify[] = ['error', 'Please select a customer and loan plan first'];
            return to_route('staff.loan.list')->withNotify($notify);
        }

        $user = $this->findEligibleCustomerById($sessionLoan['user_id']);
        if (!$user instanceof User) {
            session()->forget('staff_loan');
            return $user;
        }

        $plan   = LoanPlan::active()->findOrFail($sessionLoan['plan_id']);
        $amount = $sessionLoan['amount'];

        if (!$plan->form_id || !$plan->form) {
            $notify[] = ['error', 'This loan plan has no application form configured'];
            return to_route('staff.loan.plans', $user->account_number)->withNotify($notify);
        }

        $pageTitle = 'Loan Application Form';
        return view('branch_staff.loan.form', compact('pageTitle', 'plan', 'amount', 'user'));
    }

    public function confirm(Request $request)
    {
        $sessionLoan = session('staff_loan');
        if (!$sessionLoan) {
            $notify[] = ['error', 'Loan application session expired'];
            return to_route('staff.loan.list')->withNotify($notify);
        }

        $user = $this->findEligibleCustomerById($sessionLoan['user_id']);
        if (!$user instanceof User) {
            session()->forget('staff_loan');
            return $user;
        }

        $plan   = LoanPlan::active()->findOrFail($sessionLoan['plan_id']);
        $amount = $sessionLoan['amount'];

        if (!$plan->form) {
            $notify[] = ['error', 'This loan plan has no application form configured'];
            return to_route('staff.loan.plans', $user->account_number)->withNotify($notify);
        }

        $formData       = $plan->form->form_data;
        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $applicationForm = $formProcessor->processFormData($request, $formData);

        $staff  = authStaff();
        $branch = $this->resolveBranch($staff);
        if (!$branch) {
            $notify[] = ['error', 'No branch is assigned to this staff account'];
            return back()->withNotify($notify)->withInput();
        }

        $perInstallment = $amount * $plan->per_installment / 100;
        $percentCharge  = $perInstallment * $plan->percent_charge / 100;
        $charge         = $plan->fixed_charge + $percentCharge;

        $loan                         = new Loan();
        $loan->loan_number            = getTrx();
        $loan->user_id                = $user->id;
        $loan->branch_id              = $branch->id;
        $loan->branch_staff_id        = $staff->id;
        $loan->plan_id                = $plan->id;
        $loan->amount                 = $amount;
        $loan->per_installment        = $perInstallment;
        $loan->installment_interval   = $plan->installment_interval;
        $loan->delay_value            = $plan->delay_value;
        $loan->charge_per_installment = $charge;
        $loan->total_installment      = $plan->total_installment;
        $loan->application_form       = $applicationForm;
        $loan->status                 = Status::LOAN_PENDING;
        $loan->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $user->id;
        $adminNotification->title     = 'New loan request from ' . $branch->name . ' branch (staff: ' . $staff->name . ')';
        $adminNotification->click_url = urlPath('admin.loan.index') . '?search=' . $loan->loan_number;
        $adminNotification->save();

        try {
            notify($user, 'LOAN_APPLY', array_merge($loan->shortCodes(), [
                'account_number' => $user->account_number,
                'branch_name'    => $branch->name,
            ]));
        } catch (\Throwable $e) {
            report($e);
        }

        session()->forget('staff_loan');

        $notify[] = ['success', 'Loan application submitted successfully for ' . $user->fullname];
        return to_route('staff.loan.details', $loan->loan_number)->withNotify($notify);
    }

    public function details($loanNumber)
    {
        $loan = $this->scopedLoans()->where('loan_number', $loanNumber)->with(['user', 'plan', 'branchStaff', 'branch'])->firstOrFail();
        $pageTitle = 'Loan Details';
        return view('branch_staff.loan.details', compact('pageTitle', 'loan'));
    }

    public function installments($loanNumber)
    {
        $loan         = $this->scopedLoans()->where('loan_number', $loanNumber)->firstOrFail();
        $installments = $loan->installments()->paginate(getPaginate());
        $pageTitle    = 'Loan Installments';
        return view('branch_staff.loan.installments', compact('pageTitle', 'loan', 'installments'));
    }

    protected function scopedLoans()
    {
        $staff = authStaff();
        $loans = Loan::query();

        if ($staff->designation == Status::ROLE_ACCOUNT_OFFICER) {
            $loans->where('branch_staff_id', $staff->id);
        } else {
            $loans->where('branch_id', session('branchId'));
        }

        return $loans;
    }

    protected function findEligibleCustomer($accountNumber)
    {
        $user = User::where(function ($query) use ($accountNumber) {
            $query->where('account_number', $accountNumber)
                ->orWhere('username', $accountNumber);
        })->first();

        if (!$user) {
            $notify[] = ['error', 'Account not found'];
            return back()->withNotify($notify)->withInput();
        }

        return $this->ensureCustomerEligible($user);
    }

    protected function findEligibleCustomerById($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            $notify[] = ['error', 'Account not found'];
            return to_route('staff.loan.list')->withNotify($notify);
        }

        return $this->ensureCustomerEligible($user);
    }

    protected function ensureCustomerEligible(User $user)
    {
        if (!$user->status) {
            $notify[] = ['error', 'This account is currently banned'];
            return back()->withNotify($notify);
        }

        if (!$user->profile_complete) {
            $notify[] = ['error', 'This account profile is not completed yet'];
            return back()->withNotify($notify);
        }

        if (gs('kv') && $user->kv != Status::KYC_VERIFIED) {
            $notify[] = ['error', 'Customer KYC must be verified before applying for a loan'];
            return back()->withNotify($notify);
        }

        return $user;
    }

    protected function resolveBranch($staff)
    {
        if (session('branchId')) {
            $branch = Branch::active()->find(session('branchId'));
            if ($branch) {
                return $branch;
            }
        }

        return $staff->branch();
    }
}
