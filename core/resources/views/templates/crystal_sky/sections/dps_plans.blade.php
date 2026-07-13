@if (@gs()->modules->dps)
    @php
        $content = getContent('dps_plans.content', true);
        try {
            $plans = App\Models\DpsPlan::active()->orderBy('per_installment')->limit(3)->get();
        } catch (\Throwable $e) {
            $plans = collect();
        }
    @endphp

    @if ($content && $plans->count())
        <section class="py-120 pricing section-bg-light bg-img"
            data-background-image="{{ asset(activeTemplate(true) . 'images/thumbs/pricing-bg.png') }}">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-heading">
                            <h6 class="section-heading__subtitle">{{ __(@$content->data_values->heading) }}</h6>
                            <h2 class="section-heading__title">{{ __(@$content->data_values->subheading) }}</h2>
                        </div>
                    </div>
                </div>
                @include('Template::partials.dps_plans')
                <div class="text-center mt-4">
                    <a href="{{ route('user.dps.plans') }}" class="btn btn--base">@lang('View All')</a>
                </div>
            </div>
        </section>
    @endif
@endif
