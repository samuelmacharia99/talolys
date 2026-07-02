<?php

namespace App\Support\Tenancy;

use App\Models\Tenant;
use App\Models\Scopes\TenantScope;
use Closure;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

class TenantContext
{
    protected ?Tenant $tenant = null;

    protected bool $bypassScope = false;

    public function set(Tenant $tenant): void
    {
        $this->tenant = $tenant;
    }

    public function get(): ?Tenant
    {
        return $this->tenant;
    }

    public function id(): ?int
    {
        return $this->tenant?->id;
    }

    public function check(): Tenant
    {
        if (!$this->tenant) {
            throw new RuntimeException('No tenant context is set for this request.');
        }

        return $this->tenant;
    }

    public function has(): bool
    {
        return $this->tenant !== null;
    }

    public function clear(): void
    {
        $this->tenant = null;
        $this->bypassScope = false;
    }

    public function run(Tenant $tenant, Closure $callback): mixed
    {
        $previous = $this->tenant;
        $previousBypass = $this->bypassScope;

        $this->set($tenant);
        $this->bypassScope = false;

        try {
            return $callback($tenant);
        } finally {
            $this->tenant = $previous;
            $this->bypassScope = $previousBypass;
        }
    }

    public function runWithoutScope(Closure $callback): mixed
    {
        $previous = $this->bypassScope;
        $this->bypassScope = true;

        try {
            return $callback();
        } finally {
            $this->bypassScope = $previous;
        }
    }

    public function shouldBypassScope(): bool
    {
        return $this->bypassScope;
    }

    public function cacheKey(string $key): string
    {
        return 'tenant:' . $this->check()->id . ':' . $key;
    }

    public function isCentralHost(string $host): bool
    {
        $host = strtolower($host);
        $central = array_map('strtolower', config('tenancy.central_domains', []));

        return in_array($host, $central, true);
    }

    public function tenantRootDomain(): string
    {
        return strtolower(config('tenancy.tenant_root_domain', 'talolys.test'));
    }

    public function isTenantSubdomain(string $host): bool
    {
        $root = $this->tenantRootDomain();

        return str_ends_with(strtolower($host), '.' . $root);
    }

    public function subdomainFromHost(string $host): ?string
    {
        $host = strtolower($host);
        $root = $this->tenantRootDomain();
        $suffix = '.' . $root;

        if (!str_ends_with($host, $suffix)) {
            return null;
        }

        $sub = substr($host, 0, -strlen($suffix));

        return $sub !== '' ? $sub : null;
    }
}
