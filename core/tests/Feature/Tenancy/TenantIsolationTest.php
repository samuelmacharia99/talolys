<?php

namespace Tests\Feature\Tenancy;

use App\Models\Admin;
use App\Models\User;
use App\Services\Tenancy\TenantProvisioner;
use App\Support\Tenancy\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_are_isolated_between_tenants(): void
    {
        $provisioner = app(TenantProvisioner::class);
        $tenantA = $provisioner->provision([
            'name' => 'Bank A', 'slug' => 'banka',
            'admin_username' => 'admina', 'admin_password' => 'password',
        ]);
        $tenantB = $provisioner->provision([
            'name' => 'Bank B', 'slug' => 'bankb',
            'admin_username' => 'adminb', 'admin_password' => 'password',
        ]);

        $context = app(TenantContext::class);

        $context->run($tenantA, function () {
            $this->assertEquals(1, Admin::count());
            $this->assertEquals('admina', Admin::first()->username);
        });

        $context->run($tenantB, function () {
            $this->assertEquals(1, Admin::count());
            $this->assertEquals('adminb', Admin::first()->username);
        });
    }

    public function test_cross_tenant_update_is_blocked(): void
    {
        $provisioner = app(TenantProvisioner::class);
        $tenantA = $provisioner->provision(['name' => 'A', 'slug' => 'a', 'admin_username' => 'a', 'admin_password' => 'password']);
        $tenantB = $provisioner->provision(['name' => 'B', 'slug' => 'b', 'admin_username' => 'b', 'admin_password' => 'password']);
        $context = app(TenantContext::class);

        $adminId = null;
        $context->run($tenantA, function () use (&$adminId) {
            $adminId = Admin::first()->id;
        });

        $this->expectException(\RuntimeException::class);
        $context->run($tenantB, function () use ($adminId) {
            $admin = Admin::withoutGlobalScopes()->findOrFail($adminId);
            $admin->name = 'Hacked';
            $admin->save();
        });
    }

    public function test_users_table_scoped_per_tenant(): void
    {
        $provisioner = app(TenantProvisioner::class);
        $tenantA = $provisioner->provision(['name' => 'A', 'slug' => 'ua', 'admin_username' => 'a', 'admin_password' => 'password']);
        $tenantB = $provisioner->provision(['name' => 'B', 'slug' => 'ub', 'admin_username' => 'b', 'admin_password' => 'password']);
        $context = app(TenantContext::class);

        $context->run($tenantA, function () {
            $this->assertEquals(0, User::count());
        });

        $context->run($tenantB, function () {
            $this->assertEquals(0, User::count());
        });
    }
}
