@php
    $subscribe = getContent('subscribe.content', true);
@endphp

@if ($subscribe)
    <div class="subscribe-modern">
        <div class="container">
            <div class="subscribe-modern__wrapper">
                <div class="row align-items-center gy-3">
                    <div class="col-lg-6">
                        <h4 class="subscribe-modern__title">
                            {{ __(@$subscribe->data_values->heading) }}
                        </h4>
                        <p class="subscribe-modern__desc">@lang('Get the latest banking news, product updates, and financial tips delivered to your inbox.')</p>
                    </div>
                    <div class="col-lg-6">
                        <form class="subscribe-modern__form" id="subscribeForm">
                            @csrf
                            <div class="subscribe-modern__input-group">
                                <input required type="email" class="form--control" name="email" id="leadEmail" placeholder="@lang('Enter your email address')" />
                                <button class="btn btn--base" type="submit">@lang('Subscribe')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@push('style')
    <style>
        .subscribe-modern {
            padding: 60px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .subscribe-modern__title {
            color: #fff;
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 6px;
        }
        .subscribe-modern__desc {
            color: rgba(255,255,255,0.7);
            font-size: 14px;
        }
        .subscribe-modern__input-group {
            display: flex;
            gap: 8px;
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
            padding: 6px;
        }
        .subscribe-modern__input-group .form--control {
            border: none;
            background: transparent;
            color: #fff;
            padding: 12px 16px;
            flex-grow: 1;
        }
        .subscribe-modern__input-group .form--control::placeholder {
            color: rgba(255,255,255,0.5);
        }
        .subscribe-modern__input-group .form--control:focus {
            box-shadow: none;
            outline: none;
        }
        .subscribe-modern__input-group .btn {
            white-space: nowrap;
            padding: 12px 24px;
        }
    </style>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            var form = $("#subscribeForm");
            form.on('submit', function(e) {
                e.preventDefault();
                var data = form.serialize();
                $.ajax({
                    url: `{{ route('subscribe') }}`,
                    method: 'post',
                    data: data,
                    success: function(response) {
                        if (response.success) {
                            form.find('input[name=email]').val('');
                            notify('success', response.message);
                        } else {
                            notify('error', response.error);
                            form.find('button[type=submit]').removeAttr('disabled');
                        }
                    }
                });
            });
        })(jQuery);
    </script>
@endpush
