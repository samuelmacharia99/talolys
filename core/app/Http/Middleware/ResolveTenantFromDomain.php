<?php

namespace App\Http\Middleware;

use App\Services\Tenancy\TenantResolver;
use App\Support\Tenancy\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenantFromDomain
{
    public function __construct(
        protected TenantResolver $resolver,
        protected TenantContext $context
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();

        if ($this->context->isCentralHost($host)) {
            abort(404);
        }

        $tenant = $this->resolver->resolveFromHost($host);

        if (!$tenant) {
            abort(404, 'Bank not found or domain not verified.');
        }

        $this->context->set($tenant);

        return $next($request);
    }
}
