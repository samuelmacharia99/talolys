@extends('Template::layouts.app')
@section('app')
    @include('Template::partials.header_top')
    @include('Template::partials.header')

    @if (!request()->RouteIs('home'))
        @include('Template::partials.breadcrumb')
    @endif

    @yield('content')

    @include('Template::partials.footer')
@endsection
