<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Tenant;

class DashboardController extends Controller
{
    public function index()
    {
        $pageTitle = 'Platform Dashboard';

        return view('platform.dashboard', [
            'pageTitle'        => $pageTitle,
            'tenantCount'      => Tenant::count(),
            'activeTenants'    => Tenant::where('status', Tenant::STATUS_ACTIVE)->count(),
            'suspendedTenants' => Tenant::where('status', Tenant::STATUS_SUSPENDED)->count(),
            'recentTenants'    => Tenant::with('domains')->latest()->take(10)->get(),
        ]);
    }
}
