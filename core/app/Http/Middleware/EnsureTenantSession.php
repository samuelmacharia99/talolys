<?php

namespace App\Http\Middleware;

use App\Support\Tenancy\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantSession
{
    public function __construct(
        protected TenantContext $context
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->context->has()) {
            return $next($request);
        }

        if (!$request->hasSession()) {
            return $next($request);
        }

        $sessionKey = config('tenancy.session_tenant_key');
        $sessionTenantId = $request->session()->get($sessionKey);

        if ($sessionTenantId && (int) $sessionTenantId !== (int) $this->context->id()) {
            auth()->logout();
            auth()->guard('admin')->logout();
            auth()->guard('branch_staff')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            abort(403, 'Session is not valid for this bank domain.');
        }

        return $next($request);
    }
}
