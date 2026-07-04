@php
    $workContent = getContent('how_it_work.content', true);
    $workElement = getContent('how_it_work.element', orderById: true);
@endphp
<section class="py-120 how-it-works-modern">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7 text-center">
                <div class="section-heading">
                    <h6 class="section-heading__subtitle">{{ __(@$workContent->data_values->title) }}</h6>
                    <h2 class="section-heading__title">{{ __(@$workContent->data_values->heading) }}</h2>
                </div>
            </div>
        </div>
        <div class="row g-4 justify-content-center">
            @foreach ($workElement as $element)
                <div class="col-lg-3 col-sm-6">
                    <div class="step-card-modern text-center">
                        <div class="step-card-modern__step">
                            <span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <h5 class="step-card-modern__title">{{ __(@$element->data_values->heading) }}</h5>
                        <p class="step-card-modern__desc">{{ __(@$element->data_values->subheading) }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

@push('style')
    <style>
        .how-it-works-modern {
            background: #fff;
        }
        .step-card-modern {
            padding: 30px 20px;
            position: relative;
        }
        .step-card-modern__step {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: hsl(var(--base) / 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            position: relative;
        }
        .step-card-modern__step span {
            font-size: 20px;
            font-weight: 800;
            color: hsl(var(--base));
        }
        .step-card-modern__title {
            font-size: 17px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 10px;
        }
        .step-card-modern__desc {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.6;
        }
    </style>
@endpush
