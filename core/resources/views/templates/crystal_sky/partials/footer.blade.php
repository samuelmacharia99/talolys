@php
    $footer = getContent('footer.content', true);
    $socialLinks = getContent('social_link.element', orderById: true);
    $policyPages = getContent('policy_pages.element', orderById: true);
    $contact = getContent('contact_us.content', true);
    $cookie = App\Models\Frontend::where('data_keys', 'cookie.data')->first();
@endphp

@if (@$cookie?->data_values?->status == Status::ENABLE && !\Cookie::get('gdpr_cookie'))
    <div class="cookies-card hide text-center">
        <div class="cookies-card__icon bg--base">
            <i class="las la-cookie-bite"></i>
        </div>
        <p class="cookies-card__content mt-4">{{ @$cookie->data_values->short_desc }} <a href="{{ route('cookie.policy') }}" target="_blank"
                class="text--base">@lang('Learn more')</a></p>
        <div class="cookies-card__btn mt-4">
            <a class="btn btn--base w-100 policy" href="javascript:void(0)">@lang('Allow')</a>
        </div>
    </div>
@endif

<footer class="footer-area">
    @include('Template::sections.subscribe')
    <div class="py-60">
        <div class="container">
            <div class="row justify-content-center gy-5">
                <div class="col-xl-3 col-sm-6">
                    <div class="footer-item">
                        <div class="footer-item__logo mb-3">
                            <a href="{{ route('home') }}"><img src="{{ siteLogo() }}" alt="@lang('image')"></a>
                        </div>
                        <p class="footer-item__desc">{{ __(@$footer->data_values->description) }}</p>
                        <ul class="social-list mt-3">
                            @foreach ($socialLinks as $social)
                                <li class="social-list__item">
                                    <a href="{{ $social->data_values->social_link }}" target="_blank" class="social-list__link flex-center">
                                        @php echo $social->data_values->social_icon; @endphp
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-xl-2 col-sm-6">
                    <div class="footer-item">
                        <h5 class="footer-item__title">@lang('Quick Links')</h5>
                        <ul class="footer-menu">
                            <li class="footer-menu__item">
                                <a href="{{ route('home') }}" class="footer-menu__link">@lang('Home')</a>
                            </li>
                            @auth
                                <li class="footer-menu__item">
                                    <a href="{{ route('user.home') }}" class="footer-menu__link">@lang('Dashboard')</a>
                                </li>
                            @else
                                <li class="footer-menu__item">
                                    <a href="{{ route('user.register') }}" class="footer-menu__link">@lang('Open Account')</a>
                                </li>
                                <li class="footer-menu__item">
                                    <a href="{{ route('user.login') }}" class="footer-menu__link">@lang('Internet Banking')</a>
                                </li>
                            @endauth
                            <li class="footer-menu__item">
                                <a href="{{ route('branches') }}" class="footer-menu__link">@lang('Our Branches')</a>
                            </li>
                            <li class="footer-menu__item">
                                <a href="{{ route('contact') }}" class="footer-menu__link">@lang('Contact Us')</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-2 col-sm-6">
                    <div class="footer-item">
                        <h5 class="footer-item__title">@lang('Legal')</h5>
                        <ul class="footer-menu">
                            @foreach ($policyPages as $policy)
                                <li class="footer-menu__item">
                                    <a href="{{ route('policy.pages', $policy->slug) }}" target="_blank"
                                        class="footer-menu__link">{{ __($policy->data_values->title) }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-xl-2 col-sm-6">
                    <div class="footer-item">
                        <h5 class="footer-item__title">@lang('Products')</h5>
                        <ul class="footer-menu">
                            <li class="footer-menu__item">
                                <a href="javascript:void(0)" class="footer-menu__link">@lang('Savings Account')</a>
                            </li>
                            <li class="footer-menu__item">
                                <a href="javascript:void(0)" class="footer-menu__link">@lang('Fixed Deposit')</a>
                            </li>
                            <li class="footer-menu__item">
                                <a href="javascript:void(0)" class="footer-menu__link">@lang('Personal Loans')</a>
                            </li>
                            <li class="footer-menu__item">
                                <a href="javascript:void(0)" class="footer-menu__link">@lang('Business Loans')</a>
                            </li>
                            <li class="footer-menu__item">
                                <a href="javascript:void(0)" class="footer-menu__link">@lang('Money Transfer')</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="footer-item">
                        <h5 class="footer-item__title">{{ __(@$footer->data_values->contact_title) }}</h5>
                        <ul class="footer-contact-menu">
                            <li class="footer-contact-menu__item">
                                <div class="footer-contact-menu__item-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="footer-contact-menu__item-content">
                                    <p>{{ __(@$contact->data_values->contact_address) }}</p>
                                </div>
                            </li>
                            <li class="footer-contact-menu__item">
                                <div class="footer-contact-menu__item-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="footer-contact-menu__item-content">
                                    <a class="footer-menu__link"
                                        href="mailto:{{ __(@$contact->data_values->email_address) }}">{{ __(@$contact->data_values->email_address) }}</a>
                                </div>
                            </li>
                            <li class="footer-contact-menu__item">
                                <div class="footer-contact-menu__item-icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="footer-contact-menu__item-content">
                                    <a class="footer-menu__link"
                                        href="tel:{{ __(@$contact->data_values->contact_number) }}">{{ __(@$contact->data_values->contact_number) }}</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bottom-footer section-bg">
        <div class="container">
            <div class="row gy-3 align-items-center">
                <div class="col-md-6">
                    <p class="bottom-footer-text text-white">
                        &copy; @php echo date('Y') @endphp {{ __(gs()->site_name) }}. @lang('All Rights Reserved.')
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="bottom-footer-text text-white" style="font-size: 13px; opacity: 0.8;">
                        @lang('Regulated by the Central Bank of Kenya')
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>
