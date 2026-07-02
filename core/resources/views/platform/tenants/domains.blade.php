@extends('platform.layout')

@section('content')
<h1 class="h4 mb-3">Domains — {{ $tenant->name }}</h1>
<div class="card mb-4"><div class="card-body">
    <form method="POST" action="{{ route('platform.tenants.domains.store', $tenant) }}">@csrf
        <label class="form-label">Custom domain</label>
        <div class="input-group">
            <input type="text" name="domain" class="form-control" placeholder="banking.example.com" required>
            <button class="btn btn-primary">Add</button>
        </div>
    </form>
</div></div>
@foreach ($tenant->domains as $domain)
<div class="card mb-2"><div class="card-body">
    <strong>{{ $domain->domain }}</strong> ({{ $domain->type }})
    @if ($domain->type === 'custom' && !$domain->isVerified())
        <p class="mb-1 mt-2">Add TXT record:</p>
        <code>{{ config('tenancy.domain_verification_prefix') }}.{{ $domain->domain }} = {{ $domain->verification_token }}</code>
        <form class="mt-2" method="POST" action="{{ route('platform.domains.verify', $domain) }}">@csrf<button class="btn btn-sm btn-outline-primary">Verify now</button></form>
    @endif
</div></div>
@endforeach
@endsection
