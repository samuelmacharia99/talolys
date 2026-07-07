@extends('Template::layouts.frontend')
@section('content')

    @if (request()->routeIs('home'))
        @include('Template::sections.banner')
    @endif

    @if ($sections?->secs)
        @foreach (json_decode($sections->secs, true) ?? [] as $sec)
            @if (view()->exists('Template::sections.' . $sec))
                @include('Template::sections.' . $sec)
            @endif
        @endforeach
    @endif
@endsection
