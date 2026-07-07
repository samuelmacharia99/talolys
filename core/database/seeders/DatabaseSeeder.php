<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->ensureTenantExists();

        $this->call([
            GeneralSettingSeeder::class,
            AdminSeeder::class,
            BranchSeeder::class,
            UserSeeder::class,
            FrontendSeeder::class,
            DummyDataSeeder::class,
        ]);
    }

    protected function ensureTenantExists(): void
    {
        if (!Schema::hasTable('tenants')) {
            return;
        }

        $exists = DB::table('tenants')->where('id', 1)->exists();
        if (!$exists) {
            DB::table('tenants')->insert([
                'id'         => 1,
                'name'       => 'Talolys',
                'slug'       => 'talolys',
                'status'     => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info('Default tenant created (id=1).');
        }
    }
}
