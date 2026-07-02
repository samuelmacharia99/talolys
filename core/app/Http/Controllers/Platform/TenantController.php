<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Tenant;
use App\Services\Tenancy\TenantProvisioner;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::with(['domains', 'plan'])->latest()->paginate(20);

        return view('platform.tenants.index', [
            'pageTitle' => 'Tenants',
            'tenants'   => $tenants,
        ]);
    }

    public function create()
    {
        return view('platform.tenants.create', [
            'pageTitle' => 'Create Tenant',
            'plans'     => Plan::where('is_active', true)->get(),
        ]);
    }

    public function store(Request $request, TenantProvisioner $provisioner)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:120',
            'slug'            => ['required', 'string', 'max:60', 'alpha_dash', Rule::unique('tenants', 'slug')],
            'plan_id'         => 'nullable|exists:plans,id',
            'admin_name'      => 'required|string|max:120',
            'admin_email'     => 'required|email|max:120',
            'admin_username'  => 'required|string|max:40',
            'admin_password'  => 'required|string|min:8',
        ]);

        $plan = isset($data['plan_id']) ? Plan::find($data['plan_id']) : null;

        $tenant = $provisioner->provision([
            'name'            => $data['name'],
            'slug'            => Str::lower($data['slug']),
            'plan_slug'       => $plan?->slug,
            'admin_name'      => $data['admin_name'],
            'admin_email'     => $data['admin_email'],
            'admin_username'  => $data['admin_username'],
            'admin_password'  => $data['admin_password'],
        ], auth('platform')->id());

        $notify[] = ['success', 'Tenant provisioned successfully.'];

        return redirect()->route('platform.tenants.show', $tenant)->withNotify($notify);
    }

    public function show(Tenant $tenant)
    {
        $tenant->load(['domains', 'plan']);

        return view('platform.tenants.show', [
            'pageTitle' => $tenant->name,
            'tenant'    => $tenant,
        ]);
    }

    public function edit(Tenant $tenant)
    {
        return view('platform.tenants.edit', [
            'pageTitle' => 'Edit Tenant',
            'tenant'    => $tenant,
            'plans'     => Plan::where('is_active', true)->get(),
        ]);
    }

    public function update(Request $request, Tenant $tenant)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:120',
            'plan_id' => 'nullable|exists:plans,id',
            'status'  => 'required|in:active,suspended,pending,trialing',
        ]);

        $tenant->update($data);

        $notify[] = ['success', 'Tenant updated successfully.'];

        return back()->withNotify($notify);
    }

    public function suspend(Tenant $tenant)
    {
        $tenant->update(['status' => Tenant::STATUS_SUSPENDED]);

        $notify[] = ['success', 'Tenant suspended.'];

        return back()->withNotify($notify);
    }

    public function activate(Tenant $tenant)
    {
        $tenant->update(['status' => Tenant::STATUS_ACTIVE]);

        $notify[] = ['success', 'Tenant activated.'];

        return back()->withNotify($notify);
    }
}
