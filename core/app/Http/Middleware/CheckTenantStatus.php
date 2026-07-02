<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Support\Tenancy\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTenantStatus
{
    public function __construct(
        protected TenantContext $context
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->context->has()) {
            return $next($request);
        }

        $tenant = $this->context->check();

        if ($tenant->status === Tenant::STATUS_SUSPENDED) {
            if ($request->is('api/*')) {
                return response()->json([
                    'remark' => 'tenant_suspended',
                    'status' => 'error',
                    'message' => ['error' => ['This bank account is suspended.']],
                ], 403);
            }

            abort(503, 'This bank is currently suspended. Please contact support.');
        }

        return $next($request);
    }
}
