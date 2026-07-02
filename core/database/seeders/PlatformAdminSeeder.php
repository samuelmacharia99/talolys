<?php

namespace Database\Seeders;

use App\Models\PlatformAdmin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PlatformAdminSeeder extends Seeder
{
    public function run(): void
    {
        PlatformAdmin::updateOrCreate(['username' => 'platform'], [
            'name'     => 'Platform Admin',
            'email'    => 'platform@talolys.test',
            'password' => Hash::make('password'),
            'status'   => true,
        ]);
    }
}
