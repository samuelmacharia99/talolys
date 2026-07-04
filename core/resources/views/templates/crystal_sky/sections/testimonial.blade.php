@php
    $testimonialContent = getContent('testimonial.content', true);
    $testimonials = getContent('testimonial.element', orderById: true);
@endphp

@if ($testimonials->count())
    <section class="testimonials-modern py-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7 text-center">
                    <div class="section-heading">
                        <h6 class="section-heading__subtitle">{{ __(@$testimonialContent->data_values->heading) }}</h6>
                        <h2 class="section-heading__title">{{ __(@$testimonialContent->data_values->subheading) }}</h2>
                    </div>
                </div>
            </div>
            <div class="testimonial-slider">
                @foreach ($testimonials as $testimonial)
                    <div class="testimonails-card">
                        <div class="testimonial-modern-card">
                            <div class="testimonial-modern-card__quote">
                                <i class="las la-quote-left"></i>
                            </div>
                            <p class="testimonial-modern-card__text">
                                {{ __(@$testimonial->data_values->quote) }}
                            </p>
                            <div class="testimonial-modern-card__author">
                                <div class="testimonial-modern-card__avatar">
                                    <img src="{{ getImage('assets/images/frontend/testimonial/' . @$testimonial->data_values->image, '75x75') }}"
                                        alt="@lang('image')" />
                                </div>
                                <div class="testimonial-modern-card__info">
                                    <h5 class="testimonial-modern-card__name">{{ __(@$testimonial->data_values->name) }}</h5>
                                    <span class="testimonial-modern-card__role">{{ __(@$testimonial->data_values->designation) }}</span>
                                </div>
                                <div class="testimonial-modern-card__rating ms-auto">
                                    @php echo displayRating(floatval(@$testimonial->data_values->rating)) @endphp
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif

@push('style')
    <style>
        .testimonials-modern {
            background: #f8fafc;
        }
        .testimonial-modern-card {
            background: #fff;
            border-radius: 16px;
            padding: 36px;
            margin: 10px;
            border: 1px solid #eef2f7;
            transition: all 0.3s;
            height: 100%;
        }
        .testimonial-modern-card:hover {
            box-shadow: 0 10px 30px rgba(0,0,0,0.06);
        }
        .testimonial-modern-card__quote {
            font-size: 36px;
            color: hsl(var(--base) / 0.3);
            margin-bottom: 16px;
            line-height: 1;
        }
        .testimonial-modern-card__text {
            font-size: 15px;
            color: #4b5563;
            line-height: 1.8;
            margin-bottom: 24px;
            font-style: italic;
        }
        .testimonial-modern-card__author {
            display: flex;
            align-items: center;
            gap: 14px;
            padding-top: 20px;
            border-top: 1px solid #f3f4f6;
        }
        .testimonial-modern-card__avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            overflow: hidden;
            background: hsl(var(--base) / 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .testimonial-modern-card__avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .testimonial-modern-card__name {
            font-size: 15px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 2px;
        }
        .testimonial-modern-card__role {
            font-size: 13px;
            color: #6b7280;
        }
        .testimonial-modern-card__rating {
            color: #fbbf24;
            font-size: 14px;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $(".testimonial-slider").slick({
                slidesToShow: 2,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 4000,
                speed: 800,
                dots: true,
                pauseOnHover: true,
                arrows: false,
                responsive: [{
                        breakpoint: 1199,
                        settings: {
                            slidesToShow: 2,
                        },
                    },
                    {
                        breakpoint: 767,
                        settings: {
                            slidesToShow: 1,
                        },
                    },
                ],
            });
        })(jQuery);
    </script>
@endpush
