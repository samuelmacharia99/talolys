<?php

namespace Database\Seeders;

use App\Constants\Status;
use App\Models\BalanceTransfer;
use App\Models\Beneficiary;
use App\Models\Branch;
use App\Models\BranchStaff;
use App\Models\Deposit;
use App\Models\Dps;
use App\Models\DpsPlan;
use App\Models\Fdr;
use App\Models\FdrPlan;
use App\Models\Installment;
use App\Models\Loan;
use App\Models\LoanPlan;
use App\Models\OtherBank;
use App\Models\Subscriber;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WithdrawMethod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedBranches();
        $this->seedOtherBanks();
        $this->seedWithdrawMethods();
        $this->seedFdrPlans();
        $this->seedDpsPlans();
        $this->seedLoanPlans();
        $this->seedUserProfiles();
        $this->seedDeposits();
        $this->seedBeneficiaries();
        $this->seedTransfers();
        $this->seedFdrs();
        $this->seedDpsAccounts();
        $this->seedLoans();
        $this->seedSupportTickets();
        $this->seedSubscribers();

        $this->command->info('Dummy banking data seeded successfully.');
    }

    protected function tenantData(string $table): array
    {
        return Schema::hasColumn($table, 'tenant_id') ? ['tenant_id' => 1] : [];
    }

    protected function seedBranches(): void
    {
        $branches = [
            ['name' => 'Westlands Branch', 'code' => 'WST001', 'address' => 'Westlands Road, Nairobi', 'email' => 'westlands@talolys.com', 'mobile' => '254700000002'],
            ['name' => 'Mombasa Branch', 'code' => 'MBA001', 'address' => 'Moi Avenue, Mombasa', 'email' => 'mombasa@talolys.com', 'mobile' => '254700000003'],
            ['name' => 'Kisumu Branch', 'code' => 'KSM001', 'address' => 'Oginga Odinga Street, Kisumu', 'email' => 'kisumu@talolys.com', 'mobile' => '254700000004'],
        ];

        foreach ($branches as $data) {
            Branch::updateOrCreate(
                ['code' => $data['code']],
                array_merge($data, ['status' => Status::ENABLE], $this->tenantData('branches'))
            );
        }

        $westlands = Branch::where('code', 'WST001')->first();
        if ($westlands) {
            $staff = BranchStaff::updateOrCreate(
                ['email' => 'officer@talolys.com'],
                array_merge([
                    'name'        => 'Account Officer',
                    'mobile'      => '254700000010',
                    'designation' => 0,
                    'address'     => 'Westlands Road, Nairobi',
                    'password'    => bcrypt('password123'),
                    'status'      => Status::STAFF_ACTIVE,
                ], $this->tenantData('branch_staff'))
            );

            DB::table('assign_branch_staff')->updateOrInsert(
                ['staff_id' => $staff->id, 'branch_id' => $westlands->id],
                array_merge(['staff_id' => $staff->id, 'branch_id' => $westlands->id], $this->tenantData('assign_branch_staff'))
            );
        }

        $this->command->info('Branches seeded: Westlands, Mombasa, Kisumu.');
    }

    protected function seedOtherBanks(): void
    {
        $banks = [
            ['name' => 'KCB Bank', 'processing_time' => '1-2 hours'],
            ['name' => 'Equity Bank', 'processing_time' => '1-2 hours'],
            ['name' => 'Co-operative Bank', 'processing_time' => '2-4 hours'],
            ['name' => 'NCBA Bank', 'processing_time' => '1-2 hours'],
            ['name' => 'Absa Bank Kenya', 'processing_time' => '2-4 hours'],
        ];

        foreach ($banks as $bank) {
            OtherBank::updateOrCreate(
                ['name' => $bank['name']],
                [
                    'minimum_limit'           => 100,
                    'maximum_limit'           => 500000,
                    'daily_maximum_limit'     => 1000000,
                    'monthly_maximum_limit'   => 5000000,
                    'daily_total_transaction' => 10,
                    'monthly_total_transaction' => 50,
                    'fixed_charge'            => 50,
                    'percent_charge'          => 0.5,
                    'processing_time'         => $bank['processing_time'],
                    'instruction'             => 'Transfer to ' . $bank['name'] . ' account. Processing time: ' . $bank['processing_time'] . '.',
                    'status'                  => Status::ENABLE,
                ]
            );
        }

        $this->command->info('Kenyan banks seeded: KCB, Equity, Co-op, NCBA, Absa.');
    }

    protected function seedWithdrawMethods(): void
    {
        $methods = [
            [
                'name'           => 'M-Pesa',
                'min_limit'      => 100,
                'max_limit'      => 150000,
                'fixed_charge'   => 0,
                'percent_charge' => 0,
                'rate'           => 1,
                'currency'       => 'KES',
                'description'    => 'Withdraw directly to your M-Pesa mobile wallet.',
            ],
            [
                'name'           => 'Bank Transfer',
                'min_limit'      => 500,
                'max_limit'      => 500000,
                'fixed_charge'   => 50,
                'percent_charge' => 0.5,
                'rate'           => 1,
                'currency'       => 'KES',
                'description'    => 'Withdraw to any Kenyan bank account.',
            ],
        ];

        foreach ($methods as $method) {
            WithdrawMethod::updateOrCreate(
                ['name' => $method['name']],
                array_merge($method, ['status' => Status::ENABLE])
            );
        }

        $this->command->info('Withdraw methods seeded: M-Pesa, Bank Transfer.');
    }

    protected function seedFdrPlans(): void
    {
        $plans = [
            ['name' => '3 Month Fixed Deposit', 'minimum_amount' => 10000, 'maximum_amount' => 500000, 'installment_interval' => 30, 'interest_rate' => 8.00, 'locked_days' => 90],
            ['name' => '6 Month Fixed Deposit', 'minimum_amount' => 25000, 'maximum_amount' => 1000000, 'installment_interval' => 30, 'interest_rate' => 10.00, 'locked_days' => 180],
            ['name' => '12 Month Fixed Deposit', 'minimum_amount' => 50000, 'maximum_amount' => 5000000, 'installment_interval' => 30, 'interest_rate' => 12.50, 'locked_days' => 365],
        ];

        foreach ($plans as $plan) {
            FdrPlan::updateOrCreate(['name' => $plan['name']], array_merge($plan, ['status' => Status::ENABLE]));
        }

        $this->command->info('FDR plans seeded.');
    }

    protected function seedDpsPlans(): void
    {
        $plans = [
            ['name' => 'Daily Saver', 'per_installment' => 100, 'installment_interval' => 1, 'total_installment' => 365, 'interest_rate' => 5.00, 'final_amount' => 38325, 'delay_value' => 3, 'fixed_charge' => 0, 'percent_charge' => 0],
            ['name' => 'Weekly Saver', 'per_installment' => 500, 'installment_interval' => 7, 'total_installment' => 52, 'interest_rate' => 7.00, 'final_amount' => 27860, 'delay_value' => 7, 'fixed_charge' => 0, 'percent_charge' => 0],
            ['name' => 'Monthly Saver', 'per_installment' => 2000, 'installment_interval' => 30, 'total_installment' => 12, 'interest_rate' => 9.00, 'final_amount' => 26160, 'delay_value' => 7, 'fixed_charge' => 50, 'percent_charge' => 0],
        ];

        foreach ($plans as $plan) {
            DpsPlan::updateOrCreate(['name' => $plan['name']], array_merge($plan, ['status' => Status::ENABLE]));
        }

        $this->command->info('DPS plans seeded.');
    }

    protected function seedLoanPlans(): void
    {
        $plans = [
            ['name' => 'Personal Loan', 'minimum_amount' => 10000, 'maximum_amount' => 500000, 'per_installment' => 5.00, 'installment_interval' => 30, 'total_installment' => 12, 'instruction' => 'For personal expenses, school fees, medical bills, and emergencies.', 'delay_value' => 7, 'fixed_charge' => 500, 'percent_charge' => 1.00],
            ['name' => 'Business Loan', 'minimum_amount' => 50000, 'maximum_amount' => 2000000, 'per_installment' => 4.50, 'installment_interval' => 30, 'total_installment' => 24, 'instruction' => 'For business expansion, inventory, equipment, and working capital.', 'delay_value' => 7, 'fixed_charge' => 1000, 'percent_charge' => 0.50],
            ['name' => 'Emergency Loan', 'minimum_amount' => 5000, 'maximum_amount' => 100000, 'per_installment' => 6.00, 'installment_interval' => 14, 'total_installment' => 6, 'instruction' => 'Quick access funds for urgent needs. Fast approval within 24 hours.', 'delay_value' => 3, 'fixed_charge' => 200, 'percent_charge' => 1.50],
        ];

        foreach ($plans as $plan) {
            LoanPlan::updateOrCreate(['name' => $plan['name']], array_merge($plan, ['status' => Status::ENABLE]));
        }

        $this->command->info('Loan plans seeded.');
    }

    protected function seedUserProfiles(): void
    {
        $profiles = [
            'johndoe'     => ['city' => 'Nairobi', 'state' => 'Nairobi', 'address' => 'Kilimani, Nairobi', 'balance' => 250000],
            'janedoe'     => ['city' => 'Nairobi', 'state' => 'Nairobi', 'address' => 'Karen, Nairobi', 'balance' => 180000],
            'samwilson'   => ['city' => 'Mombasa', 'state' => 'Mombasa', 'address' => 'Nyali, Mombasa', 'balance' => 95000],
            'marykamau'   => ['city' => 'Kisumu', 'state' => 'Kisumu', 'address' => 'Milimani, Kisumu', 'balance' => 120000],
            'peternjoroge' => ['city' => 'Nakuru', 'state' => 'Nakuru', 'address' => 'Section 58, Nakuru', 'balance' => 75000],
        ];

        foreach ($profiles as $username => $data) {
            $user = User::where('username', $username)->first();
            if (!$user) {
                continue;
            }

            $user->update([
                'city'    => $data['city'],
                'state'   => $data['state'],
                'address' => $data['address'],
                'zip'     => '00100',
                'balance' => $data['balance'],
            ]);
        }

        $this->command->info('User profiles updated with Kenyan addresses and balances.');
    }

    protected function seedDeposits(): void
    {
        $branch = Branch::where('code', 'MAIN001')->first();
        $staff  = BranchStaff::where('email', 'manager@talolys.com')->first();

        if (!$branch || !$staff) {
            return;
        }

        $deposits = [
            ['username' => 'johndoe', 'amount' => 100000, 'days_ago' => 30, 'trx' => 'SEEDDEP000001'],
            ['username' => 'johndoe', 'amount' => 50000, 'days_ago' => 15, 'trx' => 'SEEDDEP000002'],
            ['username' => 'janedoe', 'amount' => 80000, 'days_ago' => 25, 'trx' => 'SEEDDEP000003'],
            ['username' => 'samwilson', 'amount' => 45000, 'days_ago' => 20, 'trx' => 'SEEDDEP000004'],
            ['username' => 'marykamau', 'amount' => 60000, 'days_ago' => 10, 'trx' => 'SEEDDEP000005'],
            ['username' => 'peternjoroge', 'amount' => 35000, 'days_ago' => 5, 'trx' => 'SEEDDEP000006'],
        ];

        foreach ($deposits as $data) {
            $user = User::where('username', $data['username'])->first();
            if (!$user) {
                continue;
            }

            $trx = $data['trx'];
            $createdAt = now()->subDays($data['days_ago']);

            Deposit::updateOrCreate(
                ['trx' => $trx],
                [
                    'user_id'         => $user->id,
                    'branch_id'       => $branch->id,
                    'branch_staff_id' => $staff->id,
                    'method_code'     => 0,
                    'method_currency' => 'KES',
                    'amount'          => $data['amount'],
                    'charge'          => 0,
                    'rate'            => 1,
                    'final_amount'    => $data['amount'],
                    'status'          => Status::PAYMENT_SUCCESS,
                    'created_at'      => $createdAt,
                    'updated_at'      => $createdAt,
                ]
            );

            if (!Transaction::where('trx', $trx)->exists()) {
                $this->createTransaction([
                    'user_id'          => $user->id,
                    'branch_id'        => $branch->id,
                    'branch_staff_id'  => $staff->id,
                    'amount'           => $data['amount'],
                    'post_balance'     => $user->balance,
                    'trx_type'         => '+',
                    'details'          => 'Deposited from ' . $branch->name . ' branch',
                    'trx'              => $trx,
                    'remark'           => 'deposit',
                    'created_at'       => $createdAt,
                ]);
            }
        }

        $this->command->info('Branch deposits seeded.');
    }

    protected function seedBeneficiaries(): void
    {
        $john = User::where('username', 'johndoe')->first();
        $jane = User::where('username', 'janedoe')->first();
        $mary = User::where('username', 'marykamau')->first();
        $peter = User::where('username', 'peternjoroge')->first();

        if (!$john || !$jane) {
            return;
        }

        $pairs = [
            ['owner' => $john, 'beneficiary' => $jane, 'short_name' => 'Jane'],
            ['owner' => $john, 'beneficiary' => $mary, 'short_name' => 'Mary'],
            ['owner' => $jane, 'beneficiary' => $john, 'short_name' => 'John'],
            ['owner' => $mary, 'beneficiary' => $peter, 'short_name' => 'Peter'],
        ];

        foreach ($pairs as $pair) {
            if (!$pair['beneficiary']) {
                continue;
            }

            Beneficiary::updateOrCreate(
                [
                    'user_id'          => $pair['owner']->id,
                    'beneficiary_type' => User::class,
                    'beneficiary_id'   => $pair['beneficiary']->id,
                ],
                [
                    'account_number' => $pair['beneficiary']->account_number,
                    'account_name'   => $pair['beneficiary']->fullname,
                    'short_name'     => $pair['short_name'],
                ]
            );
        }

        $this->command->info('Beneficiaries seeded.');
    }

    protected function seedTransfers(): void
    {
        $john = User::where('username', 'johndoe')->first();
        $jane = User::where('username', 'janedoe')->first();

        if (!$john || !$jane) {
            return;
        }

        $trx = 'SEEDTRF000001';
        $amount = 15000;
        $charge = 50;
        $createdAt = now()->subDays(7);

        BalanceTransfer::updateOrCreate(
            ['trx' => $trx],
            [
                'user_id'              => $john->id,
                'beneficiary_id'       => Beneficiary::where('user_id', $john->id)->where('beneficiary_id', $jane->id)->value('id') ?? 0,
                'amount'               => $amount,
                'base_currency_amount' => $amount,
                'charge'               => $charge,
                'status'               => Status::TRANSFER_COMPLETED,
                'created_at'           => $createdAt,
                'updated_at'           => $createdAt,
            ]
        );

        if (!Transaction::where('trx', $trx)->exists()) {
            $this->createTransaction([
                'user_id'      => $john->id,
                'amount'       => $amount + $charge,
                'charge'       => $charge,
                'post_balance' => $john->balance,
                'trx_type'     => '-',
                'details'      => 'Own bank transfer to ' . $jane->fullname,
                'trx'          => $trx,
                'remark'       => 'own_bank_transfer',
                'created_at'   => $createdAt,
            ]);

            $this->createTransaction([
                'user_id'      => $jane->id,
                'amount'       => $amount,
                'post_balance' => $jane->balance,
                'trx_type'     => '+',
                'details'      => 'Received transferred money from ' . $john->fullname,
                'trx'          => $trx,
                'remark'       => 'received_money',
                'created_at'   => $createdAt,
            ]);
        }

        $this->command->info('Sample transfers seeded.');
    }

    protected function seedFdrs(): void
    {
        $user = User::where('username', 'johndoe')->first();
        $plan = FdrPlan::where('name', '6 Month Fixed Deposit')->first();

        if (!$user || !$plan) {
            return;
        }

        $amount = 100000;
        $trx = 'SEEDFDR000001';

        Fdr::updateOrCreate(
            ['fdr_number' => $trx],
            [
                'user_id'               => $user->id,
                'plan_id'               => $plan->id,
                'amount'                => $amount,
                'per_installment'       => $amount * $plan->interest_rate / 100,
                'installment_interval'  => $plan->installment_interval,
                'profit'                => 0,
                'status'                => Status::FDR_RUNNING,
                'next_installment_date' => now()->addDays($plan->installment_interval),
                'locked_date'           => now()->addDays($plan->locked_days),
                'created_at'            => now()->subDays(45),
            ]
        );

        $this->command->info('FDR accounts seeded.');
    }

    protected function seedDpsAccounts(): void
    {
        $user = User::where('username', 'janedoe')->first();
        $plan = DpsPlan::where('name', 'Monthly Saver')->first();

        if (!$user || !$plan) {
            return;
        }

        $trx = 'SEEDDPS000001';

        $dps = Dps::updateOrCreate(
            ['dps_number' => $trx],
            [
                'user_id'                => $user->id,
                'plan_id'                => $plan->id,
                'per_installment'        => $plan->per_installment,
                'interest_rate'          => $plan->interest_rate,
                'installment_interval'   => $plan->installment_interval,
                'delay_value'            => $plan->delay_value,
                'charge_per_installment' => $plan->fixed_charge,
                'given_installment'      => 3,
                'total_installment'      => $plan->total_installment,
                'status'                 => Status::DPS_RUNNING,
                'created_at'             => now()->subDays(90),
            ]
        );

        if ($dps->installments()->count() === 0) {
            Installment::saveInstallments($dps, now()->subDays(90));
            $dps->installments()->limit(3)->update(['given_at' => now()]);
        }

        $this->command->info('DPS accounts seeded.');
    }

    protected function seedLoans(): void
    {
        $john = User::where('username', 'johndoe')->first();
        $sam  = User::where('username', 'samwilson')->first();
        $personalPlan = LoanPlan::where('name', 'Personal Loan')->first();
        $businessPlan = LoanPlan::where('name', 'Business Loan')->first();

        if (!$john || !$personalPlan) {
            return;
        }

        $amount = 200000;
        $perInstallment = $amount * $personalPlan->per_installment / 100;
        $trx = 'SEEDLOAN00001';

        $runningLoan = Loan::updateOrCreate(
            ['loan_number' => $trx],
            [
                'user_id'                => $john->id,
                'plan_id'                => $personalPlan->id,
                'amount'                 => $amount,
                'per_installment'        => $perInstallment,
                'installment_interval'   => $personalPlan->installment_interval,
                'delay_value'            => $personalPlan->delay_value,
                'charge_per_installment' => $personalPlan->fixed_charge,
                'given_installment'      => 2,
                'total_installment'      => $personalPlan->total_installment,
                'status'                 => Status::LOAN_RUNNING,
                'approved_at'            => now()->subDays(60),
                'created_at'             => now()->subDays(65),
            ]
        );

        if ($runningLoan->installments()->count() === 0) {
            Installment::saveInstallments($runningLoan, now()->subDays(30));
            $runningLoan->installments()->limit(2)->update(['given_at' => now()]);
        }

        if ($sam && $businessPlan) {
            $pendingAmount = 150000;
            Loan::updateOrCreate(
                ['loan_number' => 'SEEDLOAN00002'],
                [
                    'user_id'                => $sam->id,
                    'plan_id'                => $businessPlan->id,
                    'amount'                 => $pendingAmount,
                    'per_installment'        => $pendingAmount * $businessPlan->per_installment / 100,
                    'installment_interval'   => $businessPlan->installment_interval,
                    'delay_value'            => $businessPlan->delay_value,
                    'charge_per_installment' => $businessPlan->fixed_charge,
                    'given_installment'      => 0,
                    'total_installment'      => $businessPlan->total_installment,
                    'status'                 => Status::LOAN_PENDING,
                    'created_at'             => now()->subDays(3),
                ]
            );
        }

        $this->command->info('Loans seeded: 1 running, 1 pending.');
    }

    protected function seedSupportTickets(): void
    {
        $john = User::where('username', 'johndoe')->first();
        $jane = User::where('username', 'janedoe')->first();

        if (!$john) {
            return;
        }

        $tickets = [
            [
                'user'     => $john,
                'subject'  => 'Unable to update profile photo',
                'message'  => 'I have been trying to upload a new profile photo but keep getting an error. Please assist.',
                'priority' => 2,
                'status'   => 1,
            ],
            [
                'user'     => $jane,
                'subject'  => 'Loan application status inquiry',
                'message'  => 'I submitted a loan application last week and would like to know the current status.',
                'priority' => 3,
                'status'   => 0,
            ],
        ];

        foreach ($tickets as $index => $data) {
            if (!$data['user']) {
                continue;
            }

            $ticketRef = 'SEEDTKT' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT);

            $ticket = SupportTicket::updateOrCreate(
                ['ticket' => $ticketRef],
                [
                    'user_id'  => $data['user']->id,
                    'name'     => $data['user']->fullname,
                    'email'    => $data['user']->email,
                    'subject'  => $data['subject'],
                    'priority' => $data['priority'],
                    'status'   => $data['status'],
                    'last_reply' => now()->subDays(1),
                ]
            );

            SupportMessage::updateOrCreate(
                ['support_ticket_id' => $ticket->id],
                [
                    'admin_id' => 0,
                    'message'  => $data['message'],
                ]
            );
        }

        $this->command->info('Support tickets seeded.');
    }

    protected function seedSubscribers(): void
    {
        $emails = [
            'info@ukulimasacco.co.ke',
            'admin@boramfi.co.ke',
            'contact@pwani-savings.co.ke',
            'hello@nairobi-finance.co.ke',
        ];

        foreach ($emails as $email) {
            Subscriber::updateOrCreate(['email' => $email]);
        }

        $this->command->info('Newsletter subscribers seeded.');
    }

    protected function createTransaction(array $data): void
    {
        Transaction::create(array_merge([
            'wallet_id'        => 0,
            'virtual_card_id'  => 0,
            'branch_id'        => 0,
            'branch_staff_id'  => 0,
            'charge'           => 0,
            'updated_at'       => $data['created_at'] ?? now(),
        ], $data));
    }
}
