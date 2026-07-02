@extends('central.layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8 text-center py-5">
        <h1 class="display-5 fw-bold">Talolys</h1>
        <p class="lead text-muted">Multi-tenant digital banking platform. Each bank runs on its own domain with isolated data.</p>
        <div class="d-flex gap-3 justify-content-center mt-4">
            <a href="{{ route('central.signup') }}" class="btn btn-primary btn-lg">Start Your Bank</a>
            <a href="{{ route('platform.login') }}" class="btn btn-outline-secondary btn-lg">Platform Admin</a>
        </div>
    </div>
</div>
@endsection
