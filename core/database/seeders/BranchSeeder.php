<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\BranchStaff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $tenantData = Schema::hasColumn('branches', 'tenant_id') ? ['tenant_id' => 1] : [];

        $branch = Branch::updateOrCreate(
            ['name' => 'Main Branch'],
            array_merge([
                'code'    => 'MAIN001',
                'address' => '123 Finance Street, Nairobi',
                'status'  => 1,
            ], $tenantData)
        );

        $staffTenantData = Schema::hasColumn('branch_staff', 'tenant_id') ? ['tenant_id' => 1] : [];

        $staff = BranchStaff::updateOrCreate(
            ['email' => 'manager@talolys.com'],
            array_merge([
                'name'     => 'Branch Manager',
                'mobile'   => '254700000001',
                'password' => Hash::make('password123'),
                'status'   => 1,
            ], $staffTenantData)
        );

        DB::table('assign_branch_staff')->updateOrInsert(
            ['staff_id' => $staff->id, 'branch_id' => $branch->id],
            ['staff_id' => $staff->id, 'branch_id' => $branch->id]
        );

        $this->command->info("Branch staff created: email=manager@talolys.com, password=password123");
    }
}
