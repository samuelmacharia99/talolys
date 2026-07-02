<?php

namespace App\Jobs\Concerns;

use App\Models\Tenant;
use App\Support\Tenancy\TenantContext;

trait TenantAware
{
    public int $tenantId;

    public function initializeTenantContext(): void
    {
        $tenant = Tenant::query()->findOrFail($this->tenantId);
        app(TenantContext::class)->set($tenant);
    }
}
