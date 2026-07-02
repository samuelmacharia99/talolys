@extends('platform.layout')

@section('content')
<h1 class="h4">{{ $tenant->name }}</h1>
<p class="text-muted">Status: {{ $tenant->status }} | Plan: {{ $tenant->plan?->name ?? 'None' }}</p>
<div class="mb-3">
    <a href="{{ route('platform.tenants.domains', $tenant) }}" class="btn btn-outline-primary btn-sm">Domains</a>
    @if ($tenant->status !== 'suspended')
        <form class="d-inline" method="POST" action="{{ route('platform.tenants.suspend', $tenant) }}">@csrf<button class="btn btn-outline-danger btn-sm">Suspend</button></form>
    @else
        <form class="d-inline" method="POST" action="{{ route('platform.tenants.activate', $tenant) }}">@csrf<button class="btn btn-outline-success btn-sm">Activate</button></form>
    @endif
</div>
<ul class="list-group">
@foreach ($tenant->domains as $domain)
    <li class="list-group-item d-flex justify-content-between">
        <span>{{ $domain->domain }} <small class="text-muted">({{ $domain->type }})</small></span>
        <span>{{ $domain->isVerified() ? 'Verified' : 'Pending verification' }}</span>
    </li>
@endforeach
</ul>
@if ($tenant->primaryDomain())
    <a class="btn btn-primary mt-3" href="https://{{ $tenant->primaryDomain()->domain }}/admin" target="_blank">Open bank admin</a>
@endif
@endsection
