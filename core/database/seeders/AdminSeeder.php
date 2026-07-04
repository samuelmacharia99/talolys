<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $tenantData = Schema::hasColumn('admins', 'tenant_id') ? ['tenant_id' => 1] : [];

        Admin::updateOrCreate(
            ['username' => 'admin'],
            array_merge([
                'name'     => 'Super Admin',
                'email'    => 'admin@talolys.com',
                'password' => Hash::make('password123'),
                'status'   => 1,
            ], $tenantData)
        );

        $this->command->info('Admin user created: username=admin, password=password123');
    }
}
