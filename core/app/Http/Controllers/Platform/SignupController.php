<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Services\Tenancy\TenantProvisioner;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SignupController extends Controller
{
    public function create()
    {
        return view('central.signup', ['pageTitle' => 'Sign Up']);
    }

    public function store(Request $request, TenantProvisioner $provisioner)
    {
        $data = $request->validate([
            'bank_name'       => 'required|string|max:120',
            'slug'            => ['required', 'string', 'max:60', 'alpha_dash', Rule::unique('tenants', 'slug')],
            'admin_name'      => 'required|string|max:120',
            'admin_email'     => 'required|email|max:120',
            'admin_username'  => 'required|string|max:40',
            'admin_password'  => 'required|string|min:8|confirmed',
        ]);

        $tenant = $provisioner->provision([
            'name'            => $data['bank_name'],
            'slug'            => Str::lower($data['slug']),
            'status'          => Tenant::STATUS_TRIALING,
            'admin_name'      => $data['admin_name'],
            'admin_email'     => $data['admin_email'],
            'admin_username'  => $data['admin_username'],
            'admin_password'  => $data['admin_password'],
        ]);

        $domain = $tenant->domains()->where('type', 'subdomain')->first();
        $url = $domain ? 'https://' . $domain->domain . '/admin' : route('central.home');

        return redirect()->away($url)->with('success', 'Your bank has been created. Please log in to the admin panel.');
    }
}
