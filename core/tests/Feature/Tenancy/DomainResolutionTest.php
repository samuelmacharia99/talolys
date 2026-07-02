<?php

namespace Tests\Feature\Tenancy;

use App\Models\Domain;
use App\Models\Tenant;
use App\Services\Tenancy\TenantProvisioner;
use App\Services\Tenancy\TenantResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DomainResolutionTest extends TestCase
{
    use RefreshDatabase;

    public function test_subdomain_resolves_active_tenant(): void
    {
        $tenant = app(TenantProvisioner::class)->provision([
            'name' => 'Resolve Bank',
            'slug' => 'resolve',
            'admin_username' => 'admin',
            'admin_password' => 'password',
        ]);

        $domain = $tenant->domains()->where('type', Domain::TYPE_SUBDOMAIN)->first();
        $resolved = app(TenantResolver::class)->resolveFromHost($domain->domain);

        $this->assertNotNull($resolved);
        $this->assertEquals($tenant->id, $resolved->id);
    }

    public function test_unverified_custom_domain_does_not_resolve(): void
    {
        $tenant = app(TenantProvisioner::class)->provision([
            'name' => 'Custom Bank',
            'slug' => 'custom',
            'admin_username' => 'admin',
            'admin_password' => 'password',
        ]);

        Domain::create([
            'tenant_id' => $tenant->id,
            'domain'    => 'banking.example.com',
            'type'      => Domain::TYPE_CUSTOM,
            'verification_token' => 'token',
        ]);

        $resolved = app(TenantResolver::class)->resolveFromHost('banking.example.com');
        $this->assertNull($resolved);
    }
}
