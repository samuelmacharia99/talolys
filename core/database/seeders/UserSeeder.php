<?php

namespace Database\Seeders;

use App\Constants\Status;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::first();
        $branchId = $branch?->id ?? 0;

        $users = [
            [
                'username'   => 'johndoe',
                'firstname'  => 'John',
                'lastname'   => 'Doe',
                'email'      => 'john@talolys.com',
                'mobile'     => '254700100001',
                'dial_code'  => '+254',
            ],
            [
                'username'   => 'janedoe',
                'firstname'  => 'Jane',
                'lastname'   => 'Doe',
                'email'      => 'jane@talolys.com',
                'mobile'     => '254700100002',
                'dial_code'  => '+254',
            ],
            [
                'username'   => 'samwilson',
                'firstname'  => 'Sam',
                'lastname'   => 'Wilson',
                'email'      => 'sam@talolys.com',
                'mobile'     => '254700100003',
                'dial_code'  => '+254',
            ],
            [
                'username'   => 'marykamau',
                'firstname'  => 'Mary',
                'lastname'   => 'Kamau',
                'email'      => 'mary@talolys.com',
                'mobile'     => '254700100004',
                'dial_code'  => '+254',
            ],
            [
                'username'   => 'peternjoroge',
                'firstname'  => 'Peter',
                'lastname'   => 'Njoroge',
                'email'      => 'peter@talolys.com',
                'mobile'     => '254700100005',
                'dial_code'  => '+254',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['username' => $userData['username']],
                array_merge($userData, [
                    'password'         => Hash::make('password123'),
                    'branch_id'        => $branchId,
                    'country_code'     => 'KE',
                    'country_name'     => 'Kenya',
                    'status'           => Status::USER_ACTIVE,
                    'ev'               => Status::VERIFIED,
                    'sv'               => Status::VERIFIED,
                    'profile_complete' => Status::YES,
                    'kyc_data'         => null,
                ])
            );
        }

        $this->command->info('5 users created (password: password123): johndoe, janedoe, samwilson, marykamau, peternjoroge');
    }
}
