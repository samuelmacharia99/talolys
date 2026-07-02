@extends('platform.layout')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h1 class="h4 mb-0">Plans</h1>
    <a href="{{ route('platform.plans.create') }}" class="btn btn-primary btn-sm">New plan</a>
</div>
<div class="card"><table class="table mb-0">
<thead><tr><th>Name</th><th>Price</th><th>Users</th><th>Branches</th><th></th></tr></thead>
<tbody>
@foreach ($plans as $plan)
<tr>
    <td>{{ $plan->name }}</td>
    <td>{{ $plan->price }}</td>
    <td>{{ $plan->max_users }}</td>
    <td>{{ $plan->max_branches }}</td>
    <td><a href="{{ route('platform.plans.edit', $plan) }}">Edit</a></td>
</tr>
@endforeach
</tbody></table></div>
@endsection
