@extends('platform.layout')

@section('content')
<div class="row g-3">
    <div class="col-md-4"><div class="card"><div class="card-body"><h6>Total tenants</h6><h3>{{ $tenantCount }}</h3></div></div></div>
    <div class="col-md-4"><div class="card"><div class="card-body"><h6>Active</h6><h3>{{ $activeTenants }}</h3></div></div></div>
    <div class="col-md-4"><div class="card"><div class="card-body"><h6>Suspended</h6><h3>{{ $suspendedTenants }}</h3></div></div></div>
</div>
<div class="card mt-4">
    <div class="card-header d-flex justify-content-between">
        <span>Recent tenants</span>
        <a href="{{ route('platform.tenants.create') }}" class="btn btn-sm btn-primary">New tenant</a>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Name</th><th>Slug</th><th>Status</th><th>Domain</th></tr></thead>
            <tbody>
            @foreach ($recentTenants as $tenant)
                <tr>
                    <td><a href="{{ route('platform.tenants.show', $tenant) }}">{{ $tenant->name }}</a></td>
                    <td>{{ $tenant->slug }}</td>
                    <td>{{ $tenant->status }}</td>
                    <td>{{ $tenant->primaryDomain()?->domain }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
