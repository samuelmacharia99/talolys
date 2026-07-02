@extends('Template::layouts.master')
@section('content')
    <form action="{{ route('user.deposit.insert') }}" method="post" class="deposit-form">
        @csrf
        <input type="hidden" name="currency">
        <div class="gateway-card">
            <div class="row justify-content-center gy-sm-4 gy-3">
                <div class="col-lg-6">
                    <div class="payment-system-list is-scrollable gateway-option-list">
                        @foreach ($gatewayCurrency as $data)
                            <label for="{{ titleToKey($data->name) }}" class="payment-item gateway-option">
                                <div class="payment-item__info">
                                    <span class="payment-item__check"></span>
                                    <span class="payment-item__name">{{ __($data->name) }}</span>
                                </div>
                                <div class="payment-item__thumb">
                                    <img class="payment-item__thumb-img"
                                        src="{{ getImage(getFilePath('gateway') . '/' . $data->method->image) }}"
                                        alt="@lang('payment-thumb')">
                                </div>
                                <input class="payment-item__radio gateway-input" id="{{ titleToKey($data->name) }}" hidden
                                    data-gateway='@json($data)' type="radio" name="gateway"
                                    value="{{ $data->method_code }}" @checked(old('gateway', $loop->first) == $data->method_code)
                                    data-min-amount="{{ showAmount($data->min_amount) }}"
                                    data-max-amount="{{ showAmount($data->max_amount) }}">
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card custom--card mb-4">
                        @if ($wallet)
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    @lang('Deposit Wallet')
                                </h6>
                                <p class="wallet-name mt-2">
                                    {{ $wallet->name }}
                                    ({{ $wallet->currency->currency }})
                                </p>
                            </div>
                        @endif
                        <div class="card-body">
                            <input type="hidden" name="wallet_id" value="{{ $wallet->id ?? null }}"
                                data-currency="{{ $wallet ? $wallet->currency?->currency : null }}">
                            <div class="input-group">
                                <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                <input type="number" step="any" class="form-control form--control amount"
                                    name="amount" placeholder="@lang('Enter Amount')" value="{{ old('amount') }}"
                                    autocomplete="off">
                            </div>

                            <p class="text--base fs-14 fw-medium mt-2">
                                @lang('Limit'): <span class="gateway-limit">@lang('0.00')</span>
                            </p>
                        </div>
                    </div>

                    <div class="card custom--card">

                        <div class="card-body">
                            <div class="deposit-info">
                                <span class="deposit-info__title">
                                    @lang('Processing Charge')
                                    <span data-bs-toggle="tooltip" title="@lang('Processing charge for payment gateways')" class="proccessing-fee-info"><i
                                            class="las la-info-circle"></i> </span>
                                </span>

                                <div class="deposit-info__input">
                                    <span class="processing-fee">@lang('0.00')</span> {{ __(gs('cur_text')) }}
                                </div>
                            </div>

                            <div class="deposit-info">
                                <span class="deposit-info__title">@lang('Total')</span>
                                <span class="deposit-info__input">
                                    <span class="final-amount">@lang('0.00')</span> {{ __(gs('cur_text')) }}
                                </span>
                            </div>

                            <div class="deposit-info gateway-conversion d-none">
                                <span class="deposit-info__title">
                                    @lang('Conversion')
                                </span>
                                <span class="deposit-info__input">
                                    <span class="text"></span>
                                </span>
                            </div>

                            <div class="deposit-info conversion-currency d-none">
                                <span class="deposit-info__title">
                                    <p class="text">
                                        @lang('In') <span class="gateway-currency"></span>
                                    </p>
                                </span>
                                <span class="deposit-info__input">
                                    <span class="in-currency"></span>
                                </span>
                            </div>

                            <small class="crypto-message mt-3 d-block text--info">
                                @lang('Conversion with') <span class="gateway-currency"></span> @lang('and final value will Show on next step')
                            </small>
                            @if ($wallet)
                                <div class="deposit-info conversion-currency d-none">
                                    <span class="deposit-info__title">
                                        <p class="text fw-semibold">
                                            @lang('You will receive')
                                        </p>
                                    </span>
                                    <span class="deposit-info__input walletConversion">
                                        <span class="fw-semibold">0
                                            {{ $wallet ? $wallet->currency?->currency : null }}</span>
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <button type="submit" class="btn btn--base w-100 mt-3" disabled>
                        @lang('Confirm Deposit')
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('style')
    <style>
        .wallet-name {
            font-size: 0.875rem;
            font-weight: 400;
            line-height: 100%;
            color: hsl(var(--black)/0.8)
        }

        /* .payment-system-list.is-scrollable {
                            max-height: min(496px, 70vh);
                        } */
        .payment-system-list.is-scrollable {
            max-height: min(435px, 70vh);
        }

        body:has(.wallet-name) .payment-system-list.is-scrollable {
            max-height: min(540px, 70vh);
        }
    </style>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {

            var amount = parseFloat($('.amount').val() || 0);
            var gateway, minAmount, maxAmount;

            $('.amount').focus();

            $('.amount').on('input', function(e) {
                amount = parseFloat($(this).val());
                if (!amount) {
                    amount = 0;
                }
                calculation();
            });

            $('.gateway-input').on('change', function(e) {
                gatewayChange();
            });

            function gatewayChange() {
                let gatewayElement = $('.gateway-input:checked');
                let methodCode = gatewayElement.val();

                gateway = gatewayElement.data('gateway');
                minAmount = gatewayElement.data('min-amount');
                maxAmount = gatewayElement.data('max-amount');

                let processingFeeInfo =
                    `${parseFloat(gateway.percent_charge).toFixed(2)}% with ${parseFloat(gateway.fixed_charge).toFixed(2)} {{ __(gs('cur_text')) }} charge for payment gateway processing fees`
                $(".proccessing-fee-info").attr("data-bs-original-title", processingFeeInfo);
                calculation();
            }

            gatewayChange();

            function calculation() {
                if (!gateway) return;
                $(".gateway-limit").text(minAmount + " - " + maxAmount);

                let percentCharge = 0;
                let fixedCharge = 0;
                let totalPercentCharge = 0;

                if (amount) {
                    percentCharge = parseFloat(gateway.percent_charge);
                    fixedCharge = parseFloat(gateway.fixed_charge);
                    totalPercentCharge = parseFloat(amount / 100 * percentCharge);
                }

                let totalCharge = parseFloat(totalPercentCharge + fixedCharge);
                let totalAmount = parseFloat((amount || 0) + totalPercentCharge + fixedCharge);

                $(".final-amount").text(totalAmount.toFixed(2));
                $(".processing-fee").text(totalCharge.toFixed(2));
                $("input[name=currency]").val(gateway.currency);
                $(".gateway-currency").text(gateway.currency);

                if (amount < Number(gateway.min_amount) || amount > Number(gateway.max_amount)) {
                    $(".deposit-form button[type=submit]").attr('disabled', true);
                } else {
                    $(".deposit-form button[type=submit]").removeAttr('disabled');
                }

                let selectedCurrency = $('input[name=wallet_id]').data('currency');

                if (gateway.currency != selectedCurrency && gateway.method.crypto != 1) {
                    $('.deposit-form').addClass('adjust-height')

                    $(".gateway-conversion, .conversion-currency").removeClass('d-none');
                    $(".gateway-conversion").find('.deposit-info__input .text').html(
                        `1 {{ __(gs('cur_text')) }} = <span class="rate">${parseFloat(gateway.rate).toFixed(2)}</span>  <span class="method_currency">${gateway.currency}</span>`
                    );
                    $('.in-currency').text(parseFloat(totalAmount * gateway.rate).toFixed(gateway.method.crypto == 1 ?
                        8 : 2))
                } else {
                    $(".gateway-conversion, .conversion-currency").addClass('d-none');
                    $('.deposit-form').removeClass('adjust-height')
                }

                if (gateway.method.crypto == 1) {
                    $('.crypto-message').removeClass('d-none');
                } else {
                    $('.crypto-message').addClass('d-none');
                }

                if (selectedCurrency && amount > 0) {
                    let currencyRate = '{{ $wallet ? $wallet->currency?->currency_rate : 0 }}';
                    let currencyName = '{{ $wallet ? $wallet->currency?->currency : 0 }}';
                    $('.walletConversion').removeClass('d-none').find('span').text(parseFloat(amount * currencyRate)
                        .toFixed(2) + ' ' + currencyName);
                }
            }

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
            $('.gateway-input').change();
        })(jQuery);
    </script>
@endpush
