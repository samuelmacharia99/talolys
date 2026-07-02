<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'Talolys Platform' }}</title>
    <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrap.min.css') }}">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('platform.dashboard') }}">Talolys Platform</a>
        <div class="navbar-nav flex-row gap-3">
            <a class="nav-link" href="{{ route('platform.tenants.index') }}">Tenants</a>
            <a class="nav-link" href="{{ route('platform.plans.index') }}">Plans</a>
            <form method="POST" action="{{ route('platform.logout') }}">@csrf<button class="btn btn-sm btn-outline-light">Logout</button></form>
        </div>
    </div>
</nav>
<main class="container pb-5">
    @if (session('notify'))
        @foreach (session('notify') as $msg)
            <div class="alert alert-{{ $msg[0] == 'success' ? 'success' : 'danger' }}">{{ $msg[1] }}</div>
        @endforeach
    @endif
    @yield('content')
</main>
</body>
</html>
