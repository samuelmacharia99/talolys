<?php

namespace App\Services\Tenancy;

use App\Models\Domain;
use App\Models\Tenant;
use App\Support\Tenancy\TenantContext;
use Illuminate\Support\Facades\Cache;

class TenantResolver
{
    public function __construct(
        protected TenantContext $context
    ) {}

    public function resolveFromHost(string $host): ?Tenant
    {
        $host = strtolower($host);
        $host = explode(':', $host)[0];

        if ($this->context->isCentralHost($host)) {
            return null;
        }

        return Cache::remember(
            'domain:resolve:' . $host,
            now()->addMinutes(5),
            function () use ($host) {
                $domain = Domain::query()
                    ->where('domain', $host)
                    ->with('tenant.plan')
                    ->first();

                if (!$domain || !$domain->isResolvable()) {
                    return null;
                }

                $tenant = $domain->tenant;

                if (!$tenant || !$tenant->isActive()) {
                    return null;
                }

                return $tenant;
            }
        );
    }

    public function forgetHostCache(string $host): void
    {
        Cache::forget('domain:resolve:' . strtolower(explode(':', $host)[0]));
    }
}
