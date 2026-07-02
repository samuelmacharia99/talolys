@extends('central.layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h2 class="h4 mb-3">Create your bank</h2>
                <form method="POST" action="{{ route('central.signup.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Bank name</label>
                        <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subdomain slug</label>
                        <div class="input-group">
                            <input type="text" name="slug" class="form-control" value="{{ old('slug') }}" required>
                            <span class="input-group-text">.{{ config('tenancy.tenant_root_domain') }}</span>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label class="form-label">Admin name</label>
                        <input type="text" name="admin_name" class="form-control" value="{{ old('admin_name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Admin email</label>
                        <input type="email" name="admin_email" class="form-control" value="{{ old('admin_email') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Admin username</label>
                        <input type="text" name="admin_username" class="form-control" value="{{ old('admin_username') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="admin_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm password</label>
                        <input type="password" name="admin_password_confirmation" class="form-control" required>
                    </div>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>
                    @endif
                    <button type="submit" class="btn btn-primary w-100">Create Bank</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
