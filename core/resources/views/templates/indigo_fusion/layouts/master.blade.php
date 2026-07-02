@extends('Template::layouts.app')
@section('main-content')
    @include('Template::partials.header')
    <div class="main-wrapper">
        @include('Template::partials.breadcrumb')
        @include('Template::partials.bottom_menu')
        <div class="pt-100 pb-100 bg_img" style="background-image: url(' {{ asset(activeTemplate(true) . 'images/elements/bg1.jpg') }} ');">
            <div class="container">
                @yield('content')
            </div>
        </div>
        @include('Template::partials.footer')
    </div>

    @push('script')
        <script>
            (function($) {
                "use strict";
                $.each($('.select2'), function() {
                    $(this)
                        .wrap(`<div class="position-relative"></div>`)
                        .select2({
                            dropdownParent: $(this).parent()
                        });
                });
            })(jQuery);
        </script>
    @endpush
@endsection
