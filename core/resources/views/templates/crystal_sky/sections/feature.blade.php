@php
    $featureContent = getContent('feature.content', true);
    $features = getContent('feature.element', false, 6, true);
@endphp
@if (!blank($features))
    <div class="py-120 features section-bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7 text-center">
                    <div class="section-heading">
                        <h6 class="section-heading__subtitle">{{ __(@$featureContent->data_values->heading) }}</h6>
                        <h2 class="section-heading__title">
                            {{ __(@$featureContent->data_values->subheading) }}
                        </h2>
                    </div>
                </div>
            </div>

            <div class="row g-4 justify-content-center">
                @foreach ($features as $feature)
                    <div class="col-lg-4 col-sm-6">
                        <div class="feature-card-modern">
                            <div class="feature-card-modern__number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                            <div class="feature-card-modern__icon">
                                @php echo @$feature->data_values->icon @endphp
                            </div>
                            <h4 class="feature-card-modern__title">{{ __(@$feature->data_values->heading) }}</h4>
                            <p class="feature-card-modern__desc">
                                {{ __(@$feature->data_values->subheading) }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

@push('style')
    <style>
        .feature-card-modern {
            background: #fff;
            border-radius: 16px;
            padding: 36px 28px;
            text-align: center;
            position: relative;
            overflow: hidden;
            height: 100%;
            transition: all 0.3s ease;
            border: 1px solid #eef2f7;
        }
        .feature-card-modern:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 48px rgba(0,0,0,0.06);
        }
        .feature-card-modern__number {
            position: absolute;
            top: 16px;
            right: 20px;
            font-size: 48px;
            font-weight: 800;
            color: hsl(var(--base) / 0.05);
            line-height: 1;
        }
        .feature-card-modern__icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, hsl(var(--base) / 0.1), hsl(var(--base) / 0.05));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            color: hsl(var(--base));
            margin: 0 auto 20px;
        }
        .feature-card-modern__title {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 12px;
        }
        .feature-card-modern__desc {
            color: #6b7280;
            font-size: 15px;
            line-height: 1.6;
        }
    </style>
@endpush
