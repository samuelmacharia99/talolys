<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\PlatformAdmin;
use App\Services\Tenancy\TenantProvisioner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PlanSeeder::class,
            PlatformAdminSeeder::class,
            DemoTenantSeeder::class,
        ]);
    }
}
