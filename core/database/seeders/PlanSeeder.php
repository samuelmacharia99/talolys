<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::updateOrCreate(['slug' => 'starter'], [
            'name'            => 'Starter',
            'price'           => 99,
            'max_users'       => 500,
            'max_branches'    => 3,
            'enabled_modules' => (object) ['deposit' => 1, 'withdraw' => 1, 'loan' => 1, 'fdr' => 1, 'dps' => 1],
            'is_active'       => true,
        ]);

        Plan::updateOrCreate(['slug' => 'growth'], [
            'name'            => 'Growth',
            'price'           => 299,
            'max_users'       => 5000,
            'max_branches'    => 20,
            'enabled_modules' => (object) ['deposit' => 1, 'withdraw' => 1, 'loan' => 1, 'fdr' => 1, 'dps' => 1, 'virtual_card' => 1],
            'is_active'       => true,
        ]);
    }
}
