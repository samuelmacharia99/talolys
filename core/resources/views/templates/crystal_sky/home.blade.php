@extends('Template::layouts.frontend')
@section('content')

    @if (request()->routeIs('home'))
        @include('Template::sections.banner')
    @endif

    @if ($sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include('Template::sections.' . $sec)
        @endforeach
    @endif
@endsection
