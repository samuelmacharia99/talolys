@php
    $banner = getContent('banner.content', true);
@endphp
<section class="banner-section section-overlay">
    <div class="banner-bg-masks-group">
        <span class="bg-mask bg-circle"></span>
        <span class="bg-mask bg-polygon"></span>
    </div>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7 col-md-7">
                <div class="banner-content">
                    <span class="banner-content__tag">
                        <i class="las la-shield-alt"></i> @lang('CBK Regulated') &bull; @lang('Secure & Compliant')
                    </span>
                    <h1 class="banner-content__title">{{ __(@$banner->data_values->heading) }}</h1>
                    <p class="banner-content__desc">{{ __(@$banner->data_values->title) }}</p>
                    <div class="banner-content__bottom flex-align gap-3 flex-wrap">
                        <a href="{{ @$banner->data_values->button_link }}" class="btn btn--base btn--lg">
                            {{ __(@$banner->data_values->button_text) }}
                            <i class="las la-arrow-right"></i>
                        </a>
                        <a href="{{ route('user.register') }}" class="btn btn--outline btn--lg">
                            @lang('Open Account')
                        </a>
                    </div>
                    <div class="banner-trust-badges">
                        <div class="trust-badge">
                            <div class="trust-badge__icon"><i class="las la-university"></i></div>
                            <div class="trust-badge__text">
                                <strong>{{ __(@$banner->data_values->total_user) }}</strong>
                                <span>@lang('Active Accounts')</span>
                            </div>
                        </div>
                        <div class="trust-badge">
                            <div class="trust-badge__icon"><i class="las la-map-marker"></i></div>
                            <div class="trust-badge__text">
                                <strong>47</strong>
                                <span>@lang('Counties Served')</span>
                            </div>
                        </div>
                        <div class="trust-badge">
                            <div class="trust-badge__icon"><i class="las la-clock"></i></div>
                            <div class="trust-badge__text">
                                <strong>24/7</strong>
                                <span>@lang('Digital Banking')</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 col-md-5">
                <div class="banner-thumb">
                    <div class="banner-img">
                        <img src="{{ getImage('assets/images/frontend/banner/' . @$banner->data_values->image, '495x560') }}"
                            alt="@lang('image')" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('style')
    <style>
        .banner-content__tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 50px;
            padding: 8px 20px;
            font-size: 14px;
            color: rgba(255,255,255,0.9);
            margin-bottom: 20px;
            backdrop-filter: blur(4px);
        }
        .banner-content__tag i {
            font-size: 18px;
            color: hsl(var(--base));
        }
        .banner-content__desc {
            font-size: 17px;
            color: rgba(255,255,255,0.8);
            margin: 16px 0 30px;
            line-height: 1.7;
            max-width: 540px;
        }
        .btn--outline {
            background: transparent;
            border: 2px solid rgba(255,255,255,0.4);
            color: #fff;
            padding: 12px 28px;
            border-radius: 5px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn--outline:hover {
            background: #fff;
            color: hsl(var(--base));
            border-color: #fff;
        }
        .btn--lg {
            padding: 14px 32px;
            font-size: 16px;
        }
        .banner-trust-badges {
            display: flex;
            gap: 30px;
            margin-top: 45px;
            flex-wrap: wrap;
        }
        .trust-badge {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .trust-badge__icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: hsl(var(--base));
            border: 1px solid rgba(255,255,255,0.15);
        }
        .trust-badge__text strong {
            display: block;
            color: #fff;
            font-size: 18px;
            font-weight: 700;
            line-height: 1.2;
        }
        .trust-badge__text span {
            color: rgba(255,255,255,0.7);
            font-size: 13px;
        }
        @media (max-width: 575px) {
            .banner-trust-badges {
                gap: 20px;
            }
            .banner-content__bottom {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
@endpush
