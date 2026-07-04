@php
    $chooseContent = getContent('why_choose.content', true);
    $chooseElements = getContent('why_choose.element', orderById: true);
@endphp

@if ($chooseContent)
    <section class="py-120 chose-us-modern">
        <div class="container">
            <div class="row gy-5 align-items-center">
                <div class="col-lg-6">
                    <div class="section-heading style-left">
                        <h6 class="section-heading__subtitle">{{ __(@$chooseContent->data_values->heading) }}</h6>
                        <h2 class="section-heading__title">
                            {{ __(@$chooseContent->data_values->subheading) }}
                        </h2>
                    </div>
                    <div class="choose-list-modern">
                        @foreach ($chooseElements as $choose)
                            <div class="choose-item-modern">
                                <div class="choose-item-modern__number">
                                    <span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                </div>
                                <div class="choose-item-modern__content">
                                    <h5 class="choose-item-modern__title">{{ __(@$choose->data_values->heading) }}</h5>
                                    <p class="choose-item-modern__desc">
                                        {{ __(@$choose->data_values->description) }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="choose-right-modern">
                        <div class="choose-right-modern__img">
                            <img src="{{ getImage('assets/images/frontend/why_choose/' . @$chooseContent->data_values->image_one, '470x425') }}" alt="@lang('image')" />
                        </div>
                        <div class="choose-right-modern__badge">
                            <span class="badge-icon">
                                @php echo @$chooseContent->data_values->icon @endphp
                            </span>
                            <h4>{{ __(@$chooseContent->data_values->title) }}</h4>
                            <p>{{ __(@$chooseContent->data_values->subtitle) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif

@push('style')
    <style>
        .chose-us-modern {
            background: #f8fafc;
        }
        .choose-list-modern {
            display: flex;
            flex-direction: column;
            gap: 24px;
            margin-top: 10px;
        }
        .choose-item-modern {
            display: flex;
            gap: 20px;
            align-items: flex-start;
        }
        .choose-item-modern__number {
            min-width: 48px;
            height: 48px;
            border-radius: 12px;
            background: hsl(var(--base) / 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: 700;
            color: hsl(var(--base));
        }
        .choose-item-modern__title {
            font-size: 17px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 6px;
        }
        .choose-item-modern__desc {
            font-size: 15px;
            color: #6b7280;
            line-height: 1.6;
        }
        .choose-right-modern {
            position: relative;
            padding: 20px;
        }
        .choose-right-modern__img {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
        }
        .choose-right-modern__img img {
            width: 100%;
            height: auto;
            display: block;
        }
        .choose-right-modern__badge {
            position: absolute;
            bottom: 40px;
            left: -10px;
            background: #fff;
            border-radius: 12px;
            padding: 20px 24px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 200px;
        }
        .choose-right-modern__badge .badge-icon {
            font-size: 32px;
            color: hsl(var(--base));
            display: block;
            margin-bottom: 8px;
        }
        .choose-right-modern__badge h4 {
            font-size: 16px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 4px;
        }
        .choose-right-modern__badge p {
            font-size: 13px;
            color: #6b7280;
        }
    </style>
@endpush
