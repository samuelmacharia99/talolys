<?php

namespace Database\Seeders;

use App\Services\Tenancy\TenantProvisioner;
use Illuminate\Database\Seeder;

class DemoTenantSeeder extends Seeder
{
    public function run(): void
    {
        app(TenantProvisioner::class)->provision([
            'name'            => 'Demo Bank',
            'slug'            => 'demo',
            'plan_slug'       => 'starter',
            'admin_name'      => 'Demo Admin',
            'admin_email'     => 'admin@demo.test',
            'admin_username'  => 'admin',
            'admin_password'  => 'password',
        ]);
    }
}
