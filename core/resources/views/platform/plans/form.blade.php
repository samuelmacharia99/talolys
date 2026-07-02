@extends('platform.layout')

@section('content')
<h1 class="h4 mb-3">{{ $pageTitle }}</h1>
<div class="card"><div class="card-body">
<form method="POST" action="{{ $plan->exists ? route('platform.plans.update', $plan) : route('platform.plans.store') }}">
@csrf @if($plan->exists) @method('PUT') @endif
<div class="row g-3">
    <div class="col-md-6"><label class="form-label">Name</label><input name="name" class="form-control" value="{{ old('name', $plan->name) }}" required></div>
    <div class="col-md-6"><label class="form-label">Slug</label><input name="slug" class="form-control" value="{{ old('slug', $plan->slug) }}" required></div>
    <div class="col-md-4"><label class="form-label">Price</label><input name="price" type="number" step="0.01" class="form-control" value="{{ old('price', $plan->price ?? 0) }}" required></div>
    <div class="col-md-4"><label class="form-label">Max users</label><input name="max_users" type="number" class="form-control" value="{{ old('max_users', $plan->max_users ?? 100) }}" required></div>
    <div class="col-md-4"><label class="form-label">Max branches</label><input name="max_branches" type="number" class="form-control" value="{{ old('max_branches', $plan->max_branches ?? 5) }}" required></div>
    <div class="col-md-6"><label class="form-label">Stripe price ID</label><input name="stripe_price_id" class="form-control" value="{{ old('stripe_price_id', $plan->stripe_price_id) }}"></div>
    <div class="col-md-6 form-check mt-4"><input type="checkbox" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $plan->is_active ?? true))><label class="form-check-label">Active</label></div>
</div>
<button class="btn btn-primary mt-3">Save</button>
</form>
</div></div>
@endsection
