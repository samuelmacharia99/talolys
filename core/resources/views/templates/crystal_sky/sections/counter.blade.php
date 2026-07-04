@php
    $counterContent = getContent('counter.content', true);
    $counters = getContent('counter.element', orderById: true);
@endphp
<section class="counter-modern section-overlay">
    <div class="container">
        <div class="counter-modern__wrapper">
            <div class="row g-4">
                @foreach ($counters as $counter)
                    <div class="col-6 col-md-3">
                        <div class="counter-modern-card counterup-item text-center">
                            <h3 class="counter-modern-card__digit">
                                <span class="odometer" data-odometer-final="{{ @$counter->data_values->digit }}"></span>
                                <span class="counter-modern-card__symbol">{{ @$counter->data_values->symbol }}</span>
                            </h3>
                            <p class="counter-modern-card__label">
                                {{ __(@$counter->data_values->title) }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

@push('style')
    <style>
        .counter-modern {
            padding: 80px 0;
            background: linear-gradient(135deg, hsl(var(--base)) 0%, hsl(var(--base) / 0.85) 100%);
        }
        .counter-modern__wrapper {
            padding: 0;
        }
        .counter-modern-card {
            padding: 20px;
        }
        .counter-modern-card__digit {
            font-size: 42px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 8px;
        }
        .counter-modern-card__symbol {
            font-size: 32px;
            color: rgba(255,255,255,0.8);
        }
        .counter-modern-card__label {
            font-size: 15px;
            color: rgba(255,255,255,0.8);
            font-weight: 500;
        }
        @media (max-width: 575px) {
            .counter-modern-card__digit {
                font-size: 28px;
            }
        }
    </style>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset(activeTemplate(true) . 'css/odometer.css') }}" />
@endpush

@push('script-lib')
    <script src="{{ asset(activeTemplate(true) . 'js/viewport.jquery.js') }}"></script>
    <script src="{{ asset(activeTemplate(true) . 'js/odometer.min.js') }}"></script>
@endpush
