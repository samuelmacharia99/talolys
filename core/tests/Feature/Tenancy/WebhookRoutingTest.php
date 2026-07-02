<?php

namespace Tests\Feature\Tenancy;

use App\Services\Tenancy\TenantProvisioner;
use App\Support\Tenancy\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebhookRoutingTest extends TestCase
{
    use RefreshDatabase;

    public function test_slug_prefixed_ipn_resolves_tenant_context(): void
    {
        $tenant = app(TenantProvisioner::class)->provision([
            'name' => 'Webhook Bank',
            'slug' => 'webhook',
            'admin_username' => 'admin',
            'admin_password' => 'password',
        ]);

        $context = app(TenantContext::class);
        $context->clear();

        $response = $this->post('/ipn/' . $tenant->slug . '/stripe', []);

        $this->assertTrue($context->has());
        $this->assertEquals($tenant->id, $context->id());
        $this->assertNotEquals(404, $response->getStatusCode());
    }

    public function test_unknown_tenant_slug_returns_not_found(): void
    {
        $this->withoutExceptionHandling();

        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);

        $this->post('/ipn/unknown-bank/stripe', []);
    }
}
