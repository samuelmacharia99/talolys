@extends('Template::layouts.frontend')
@section('content')
    @if ($sections != null)
        @foreach (json_decode($sections, true) ?? [] as $sec)
            @if (view()->exists('Template::sections.' . $sec))
                @include('Template::sections.' . $sec)
            @endif
        @endforeach
    @endif
@endsection
