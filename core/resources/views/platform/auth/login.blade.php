@extends('platform.layout')

@section('content')
<div class="card shadow-sm" style="max-width:420px;margin:4rem auto;">
    <div class="card-body p-4">
        <h1 class="h4 mb-3">Platform login</h1>
        <form method="POST" action="{{ route('platform.login.submit') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            @if ($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif
            <button class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</div>
@endsection
