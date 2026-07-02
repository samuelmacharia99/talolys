<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Services\Tenancy\TenantResolver;
use App\Support\Tenancy\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenantFromSlug
{
    public function __construct(
        protected TenantContext $context
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $slug = $request->route('tenant');

        if (!$slug) {
            abort(404);
        }

        $tenant = Tenant::query()->where('slug', $slug)->first();

        if (!$tenant || !$tenant->isActive()) {
            abort(404, 'Bank not found.');
        }

        $this->context->set($tenant);

        return $next($request);
    }
}
