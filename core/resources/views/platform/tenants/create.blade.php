@extends('platform.layout')

@section('content')
<h1 class="h4 mb-3">Create tenant</h1>
<div class="card"><div class="card-body">
<form method="POST" action="{{ route('platform.tenants.store') }}">@csrf
@include('platform.tenants._form')
<button class="btn btn-primary">Provision tenant</button>
</form>
</div></div>
@endsection
