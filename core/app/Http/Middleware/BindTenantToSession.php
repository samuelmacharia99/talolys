<?php

namespace App\Http\Middleware;

use App\Support\Tenancy\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BindTenantToSession
{
    public function __construct(
        protected TenantContext $context
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->context->has() && auth()->check()) {
            $request->session()->put(
                config('tenancy.session_tenant_key'),
                $this->context->id()
            );
        }

        if ($this->context->has() && auth()->guard('admin')->check()) {
            $request->session()->put(
                config('tenancy.session_tenant_key'),
                $this->context->id()
            );
        }

        if ($this->context->has() && auth()->guard('branch_staff')->check()) {
            $request->session()->put(
                config('tenancy.session_tenant_key'),
                $this->context->id()
            );
        }

        return $next($request);
    }
}
