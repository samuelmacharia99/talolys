@extends('platform.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Tenants</h1>
    <a href="{{ route('platform.tenants.create') }}" class="btn btn-primary btn-sm">Create tenant</a>
</div>
<div class="card">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Name</th><th>Slug</th><th>Plan</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @foreach ($tenants as $tenant)
                <tr>
                    <td>{{ $tenant->name }}</td>
                    <td>{{ $tenant->slug }}</td>
                    <td>{{ $tenant->plan?->name ?? '—' }}</td>
                    <td>{{ $tenant->status }}</td>
                    <td><a href="{{ route('platform.tenants.show', $tenant) }}">Manage</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $tenants->links() }}</div>
</div>
@endsection
