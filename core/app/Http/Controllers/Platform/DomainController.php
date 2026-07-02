<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Models\Tenant;
use App\Services\Tenancy\DomainVerificationService;
use Illuminate\Http\Request;

class DomainController extends Controller
{
    public function index(Tenant $tenant)
    {
        return view('platform.tenants.domains', [
            'pageTitle' => 'Domains — ' . $tenant->name,
            'tenant'    => $tenant->load('domains'),
        ]);
    }

    public function store(Request $request, Tenant $tenant, DomainVerificationService $verification)
    {
        $data = $request->validate([
            'domain' => 'required|string|max:255|unique:domains,domain',
        ]);

        $domain = $verification->createCustomDomain($tenant->id, $data['domain']);

        $notify[] = ['success', 'Custom domain added. Complete DNS verification to activate it.'];

        return back()->withNotify($notify)->with('new_domain', $domain);
    }

    public function verify(Domain $domain, DomainVerificationService $verification)
    {
        if ($verification->verify($domain)) {
            $notify[] = ['success', 'Domain verified successfully.'];
        } else {
            $notify[] = ['error', 'DNS TXT record not found yet. Please try again after propagation.'];
        }

        return back()->withNotify($notify);
    }
}
