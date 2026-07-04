@php
    $aboutContent = getContent('about.content', true);
    $aboutElement = getContent('about.element', orderById: true);
@endphp
@if ($aboutContent)
    <section class="py-120 about-section">
        <div class="container">
            <div class="row gy-5 align-items-center">
                <div class="col-lg-6 order-lg-1 order-2">
                    <div class="about-thumb-modern">
                        <div class="about-thumb-modern__main">
                            <img src="{{ getImage('assets/images/frontend/about/' . @$aboutContent->data_values->image, '385x460') }}"
                                alt="@lang('image')" />
                        </div>
                        <div class="about-thumb-modern__stat">
                            <div class="stat-card">
                                <span class="stat-card__icon">
                                    @php echo @$aboutContent->data_values->image_popup_icon @endphp
                                </span>
                                <h4 class="stat-card__digit">{{ __(@$aboutContent->data_values->image_popup_digit) }}</h4>
                                <p class="stat-card__text">{{ __(@$aboutContent->data_values->image_popup_title) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 order-lg-2 order-1">
                    <div class="section-heading style-left">
                        <h6 class="section-heading__subtitle">{{ __(@$aboutContent->data_values->heading) }}</h6>
                        <h2 class="section-heading__title">
                            {{ __(@$aboutContent->data_values->subheading) }}
                        </h2>
                    </div>
                    <div class="about-tabs-modern">
                        <ul class="nav nav-pills about-tab-nav" role="tablist">
                            @foreach ($aboutElement as $about)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link @if ($loop->first) active @endif" id="pills-{{ $loop->iteration }}-tab"
                                        data-bs-toggle="pill" data-bs-target="#pills-{{ $loop->iteration }}" type="button" role="tab"
                                        aria-controls="pills-{{ $loop->iteration }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                        {{ __(@$about->data_values->heading) }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                        <div class="tab-content about-tab-content">
                            @foreach ($aboutElement as $about)
                                <div class="tab-pane fade @if ($loop->first) show active @endif" id="pills-{{ $loop->iteration }}"
                                    role="tabpanel" aria-labelledby="pills-{{ $loop->iteration }}-tab" tabindex="0">
                                    <p class="about-tab-text">
                                        {{ __(@$about->data_values->description) }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="about-cta">
                        <a href="{{ route('contact') }}" class="btn btn--base">@lang('Learn More About Us') <i class="las la-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif

@push('style')
    <style>
        .about-thumb-modern {
            position: relative;
            padding: 20px;
        }
        .about-thumb-modern__main {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        }
        .about-thumb-modern__main img {
            width: 100%;
            height: auto;
            display: block;
        }
        .about-thumb-modern__stat {
            position: absolute;
            bottom: 0;
            right: 0;
            z-index: 2;
        }
        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            text-align: center;
            min-width: 160px;
        }
        .stat-card__icon {
            font-size: 28px;
            color: hsl(var(--base));
            margin-bottom: 8px;
            display: block;
        }
        .stat-card__digit {
            font-size: 28px;
            font-weight: 800;
            color: #1a1a2e;
        }
        .stat-card__text {
            font-size: 13px;
            color: #6b7280;
            margin-top: 4px;
        }
        .about-tab-nav {
            display: flex;
            gap: 8px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .about-tab-nav .nav-link {
            background: #f3f4f6;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
            font-size: 14px;
            color: #374151;
            transition: all 0.3s;
        }
        .about-tab-nav .nav-link.active {
            background: hsl(var(--base));
            color: #fff;
        }
        .about-tab-text {
            color: #6b7280;
            font-size: 15px;
            line-height: 1.8;
        }
        .about-cta {
            margin-top: 30px;
        }
    </style>
@endpush
