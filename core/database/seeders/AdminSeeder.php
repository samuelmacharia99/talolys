<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::updateOrCreate(
            ['username' => 'admin'],
            [
                'name'     => 'Super Admin',
                'email'    => 'admin@talolys.com',
                'password' => Hash::make('password123'),
                'status'   => 1,
            ]
        );

        $this->command->info('Admin user created: username=admin, password=password123');
    }
}
