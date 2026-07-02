<?php

namespace App\Http\Middleware;

use App\Support\Tenancy\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCentralDomain
{
    public function __construct(
        protected TenantContext $context
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->context->isCentralHost($request->getHost())) {
            abort(404);
        }

        $this->context->clear();

        return $next($request);
    }
}
