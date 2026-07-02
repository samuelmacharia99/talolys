@extends('platform.layout')

@section('content')
<h1 class="h4 mb-3">Edit tenant: {{ $tenant->name }}</h1>
<div class="card"><div class="card-body">
<form method="POST" action="{{ route('platform.tenants.update', $tenant) }}">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $tenant->name) }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Plan</label>
        <select name="plan_id" class="form-select">
            <option value="">— None —</option>
            @foreach($plans as $plan)
                <option value="{{ $plan->id }}" @selected(old('plan_id', $tenant->plan_id) == $plan->id)>{{ $plan->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select" required>
            @foreach(['active', 'suspended', 'pending', 'trialing'] as $status)
                <option value="{{ $status }}" @selected(old('status', $tenant->status) === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </div>
    <button class="btn btn-primary">Save changes</button>
    <a href="{{ route('platform.tenants.show', $tenant) }}" class="btn btn-link">Cancel</a>
</form>
</div></div>
@endsection
