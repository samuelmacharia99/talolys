@php
    $serviceContent = getContent('service.content', true);
    $services = getContent('service.element', false, 6, true);
@endphp

@if ($serviceContent)
    <section class="services-section py-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7 text-center">
                    <div class="section-heading">
                        <h6 class="section-heading__subtitle">{{ __(@$serviceContent->data_values->heading) }}</h6>
                        <h2 class="section-heading__title">{{ __(@$serviceContent->data_values->subheading) }}</h2>
                    </div>
                </div>
            </div>
            <div class="row g-4 justify-content-center">
                @foreach ($services as $service)
                    <div class="col-lg-4 col-sm-6">
                        <div class="service-card-modern">
                            <div class="service-card-modern__icon">
                                @php echo @$service->data_values->icon @endphp
                            </div>
                            <div class="service-card-modern__content">
                                <h5 class="service-card-modern__title">{{ __(@$service->data_values->heading) }}</h5>
                                <p class="service-card-modern__desc">{{ __(@$service->data_values->description) }}</p>
                            </div>
                            <div class="service-card-modern__arrow">
                                <i class="las la-arrow-right"></i>
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
        .service-card-modern {
            background: #fff;
            border-radius: 12px;
            padding: 32px 28px;
            position: relative;
            transition: all 0.3s ease;
            border: 1px solid #eef2f7;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .service-card-modern:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            border-color: hsl(var(--base) / 0.2);
        }
        .service-card-modern__icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            background: hsl(var(--base) / 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: hsl(var(--base));
            margin-bottom: 20px;
        }
        .service-card-modern__title {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 10px;
        }
        .service-card-modern__desc {
            color: #6b7280;
            font-size: 15px;
            line-height: 1.6;
            flex-grow: 1;
        }
        .service-card-modern__arrow {
            margin-top: 20px;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: hsl(var(--base) / 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            color: hsl(var(--base));
            transition: all 0.3s;
        }
        .service-card-modern:hover .service-card-modern__arrow {
            background: hsl(var(--base));
            color: #fff;
        }
    </style>
@endpush
