<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Support\Tenancy\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforcePlanLimits
{
    public function __construct(
        protected TenantContext $context
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->context->has()) {
            return $next($request);
        }

        $tenant = $this->context->check()->loadMissing('plan');

        if ($tenant->plan && $request->is('user/register*', 'api/register')) {
            $userCount = \App\Models\User::count();
            if ($userCount >= $tenant->plan->max_users) {
                abort(403, 'User limit reached for your plan.');
            }
        }

        if ($tenant->plan && $request->is('admin/branch/store', 'admin/branch/create')) {
            $branchCount = \App\Models\Branch::count();
            if ($branchCount >= $tenant->plan->max_branches) {
                abort(403, 'Branch limit reached for your plan.');
            }
        }

        $request->attributes->set('tenant_plan', $tenant->plan);

        return $next($request);
    }
}
