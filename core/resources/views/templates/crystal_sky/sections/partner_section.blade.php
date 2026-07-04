@php
    $partnerContent = getContent('partner_section.content', true);
    $partners = getContent('partner_section.element', orderById: true);
@endphp

@if ($partnerContent)
    <div class="partner-modern py-60">
        <div class="container">
            <p class="partner-modern__label text-center">{{ __($partnerContent->data_values->heading) }}</p>
            <div class="brand-logos brand-slider">
                @foreach ($partners as $partner)
                    <div class="partner-modern__item">
                        <img src="{{ getImage('assets/images/frontend/partner_section/' . @$partner->data_values->image, '300x150') }}" alt="@lang('partner')">
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

@push('style')
    <style>
        .partner-modern {
            background: #f8fafc;
            border-top: 1px solid #eef2f7;
            border-bottom: 1px solid #eef2f7;
        }
        .partner-modern__label {
            font-size: 14px;
            font-weight: 600;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 30px;
        }
        .partner-modern__item {
            padding: 10px 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .partner-modern__item img {
            max-height: 45px;
            width: auto;
            filter: grayscale(100%);
            opacity: 0.6;
            transition: all 0.3s;
        }
        .partner-modern__item img:hover {
            filter: grayscale(0);
            opacity: 1;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $(".brand-slider").slick({
                slidesToShow: 5,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 2000,
                pauseOnHover: true,
                speed: 1500,
                dots: false,
                arrows: false,
                responsive: [{
                        breakpoint: 1199,
                        settings: { slidesToShow: 4 },
                    },
                    {
                        breakpoint: 767,
                        settings: { slidesToShow: 3 },
                    },
                    {
                        breakpoint: 480,
                        settings: { slidesToShow: 2 },
                    },
                ],
            });
        })(jQuery);
    </script>
@endpush
