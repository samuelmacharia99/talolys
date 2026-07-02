<?php

namespace Tests\Feature\Tenancy;

use App\Models\Tenant;
use App\Services\Tenancy\TenantProvisioner;
use App\Support\Tenancy\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SessionTenantMismatchTest extends TestCase
{
    use RefreshDatabase;

    public function test_session_from_another_tenant_is_rejected(): void
    {
        $provisioner = app(TenantProvisioner::class);
        $tenantA = $provisioner->provision(['name' => 'A', 'slug' => 'a', 'admin_username' => 'a', 'admin_password' => 'password']);
        $tenantB = $provisioner->provision(['name' => 'B', 'slug' => 'b', 'admin_username' => 'b', 'admin_password' => 'password']);

        $domainB = $tenantB->domains()->where('type', 'subdomain')->first()->domain;

        $response = $this->withSession([
            config('tenancy.session_tenant_key') => $tenantA->id,
        ])->get('http://' . $domainB . '/user/login');

        $response->assertForbidden();
    }
}
