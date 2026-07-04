@php
    $faq = getContent('faq.content', true);
    $faqs = getContent('faq.element', orderById: true);
@endphp
@if ($faq)
    <section class="py-120 faq-modern">
        <div class="container">
            <div class="row gy-5">
                <div class="col-lg-4">
                    <div class="section-heading style-left">
                        <h6 class="section-heading__subtitle">{{ __(@$faq->data_values->heading) }}</h6>
                        <h2 class="section-heading__title">
                            {{ __(@$faq->data_values->subheading) }}
                        </h2>
                    </div>
                    <p class="faq-modern__desc">{{ __(@$faq->data_values->description) }}</p>
                    <div class="faq-modern__cta">
                        <p class="faq-modern__cta-text">@lang('Still have questions?')</p>
                        <a href="{{ @$faq->data_values->button_link }}" class="btn btn--base">
                            {{ __(@$faq->data_values->button_text) }}
                            <i class="las la-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="accordion accordion-flush faq-accordion-modern" id="talolys-faq">
                        @foreach ($faqs as $faqItem)
                            <div class="accordion-item faq-accordion-modern__item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button @if (!$loop->first) collapsed @endif" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#id-{{ $loop->iteration }}-faq"
                                        @if ($loop->first) aria-expanded="true" @else aria-expanded="false" @endif
                                        aria-controls="id-{{ $loop->iteration }}-faq">
                                        {{ __(@$faqItem->data_values->question) }}
                                    </button>
                                </h2>
                                <div id="id-{{ $loop->iteration }}-faq"
                                    class="accordion-collapse collapse @if ($loop->first) show @endif" data-bs-parent="#talolys-faq">
                                    <div class="accordion-body">
                                        {{ __(@$faqItem->data_values->answer) }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif

@push('style')
    <style>
        .faq-modern {
            background: #fff;
        }
        .faq-modern__desc {
            color: #6b7280;
            font-size: 15px;
            line-height: 1.7;
            margin-bottom: 30px;
        }
        .faq-modern__cta {
            padding: 24px;
            background: hsl(var(--base) / 0.05);
            border-radius: 12px;
        }
        .faq-modern__cta-text {
            font-size: 15px;
            color: #374151;
            margin-bottom: 12px;
            font-weight: 600;
        }
        .faq-accordion-modern__item {
            border: 1px solid #eef2f7 !important;
            border-radius: 10px !important;
            margin-bottom: 12px;
            overflow: hidden;
        }
        .faq-accordion-modern__item .accordion-button {
            font-weight: 600;
            font-size: 15px;
            color: #1a1a2e;
            padding: 18px 24px;
            background: #fff;
        }
        .faq-accordion-modern__item .accordion-button:not(.collapsed) {
            background: hsl(var(--base) / 0.03);
            color: hsl(var(--base));
            box-shadow: none;
        }
        .faq-accordion-modern__item .accordion-body {
            padding: 0 24px 18px;
            color: #6b7280;
            font-size: 15px;
            line-height: 1.7;
        }
    </style>
@endpush
