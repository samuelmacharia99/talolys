@extends('Template::layouts.app')
@section('main-content')
    @include('Template::partials.header')
    <div class="main-wrapper">
        @include('Template::partials.breadcrumb')
        @yield('content')
        @include('Template::partials.footer')
    </div>
@endsection
