<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'Talolys' }}</title>
    <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrap.min.css') }}">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('central.home') }}">Talolys</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="{{ route('central.signup') }}">Sign Up</a>
            <a class="nav-link" href="{{ route('platform.login') }}">Platform Login</a>
        </div>
    </div>
</nav>
<main class="container pb-5">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @yield('content')
</main>
</body>
</html>
