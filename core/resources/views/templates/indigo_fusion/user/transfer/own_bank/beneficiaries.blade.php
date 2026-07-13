@extends('Template::layouts.master')
@section('content')
    @if (@gs()->modules->own_bank)
        <div class="d-flex flex-wrap justify-content-end mb-3">
            <a class="btn btn-sm btn--dark" href="{{ route('user.beneficiary.own') }}"> <i class="la la-users"></i> @lang('Manage Beneficiaries')</a>
        </div>
    @endif

    <div class="custom--card">
        <div class="table-responsive--md">
            <table class="custom--table table">
                <thead>
                    <tr>
                        <th>@lang('Name')</th>
                        <th>@lang('Account No.')</th>
                        <th>@lang('Account Name')</th>
                        <th>@lang('Details')</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($beneficiaries as $beneficiary)
                        <tr>
                            <td>{{ $beneficiary->short_name }}</td>
                            <td>{{ $beneficiary->account_number }} </td>
                            <td>{{ $beneficiary->account_name }}</td>
                            <td>
                                <button class="btn btn-sm btn-outline--base sendBtn" data-id="{{ $beneficiary->id }}">
                                    <i class="las la-hand-holding-usd"></i> @lang('Transfer Money')
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center" colspan="100%">@lang($emptyMessage)</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($beneficiaries->hasPages())
    <div class="mt-3">
        {{ paginateLinks($beneficiaries) }}
    </div>
    @endif
@endsection

<x-transfer-bottom-menu />

@push('modal')
    <div class="modal fade" id="sendModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Transfer Money')</h5>
                    <span class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </span>
                </div>
                <form action="" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label required">@lang('Wallet')</label>
                            <select name="wallet_id" class="form--control select2">
                                <option value="" data-currency="{{ gs('cur_text') }}"
                                    data-symbol="{{ gs('cur_sym') }}" data-rate="1">@lang('Default Wallet') ({{ showAmount(auth()->user()->balance) }})</option>
                                @foreach ($wallets as $wallet)
                                    <option value="{{ $wallet->id }}" data-currency="{{ @$wallet->currency->currency }}"
                                        data-symbol="{{ @$wallet->currency->symbol }}"
                                        data-rate="{{ @$wallet->currency->currency_rate }}">{{ $wallet->name }}
                                        ({{ @$wallet->currency->symbol }} {{ showAmount($wallet->balance, currencyFormat: false) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="required">@lang('Amount')</label>
                            <div class="input-group">
                                <input class="form--control" name="amount" type="text" required>
                                <span class="input-group-text currency"></span>
                            </div>
                        </div>

                        @include('Template::partials.otp_field')

                        <div class="my-4">
                            <ul class="caption-list-two p-0">
                                <li>
                                    <span class="caption">@lang('Limit Per Transaction')</span>
                                    <span class="value"><span class="limitPerTrans"></span> (@lang('Min'))</span>
                                </li>

                                <li>
                                    <span class="caption">@lang('Daily Limit')</span>
                                    <span class="value"><span class="dailyLimit"></span> (@lang('Max'))</span>
                                </li>

                                <li>
                                    <span class="caption">@lang('Monthly Limit')</span>
                                    <span class="value"><span class="monthlyLimit"></span> (@lang('Max'))</span>
                                </li>

                                @php $transferCharge = gs()->transferCharge(); @endphp

                                @if ($transferCharge)
                                    <li>
                                        <span class="caption">@lang('Charge Per Transaction')</span>
                                        <span class="value text--danger transCharge"> {{ $transferCharge }}</span>
                                    </li>
                                @endif
                            </ul>
                        </div>

                        <button class="btn btn--base w-100" type="submit">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endpush

@push('script')
    <script>
        'use strict';
        (function($) {
            let currency = '{{ gs("cur_text") }}';
            let symbol = '{{ gs("cur_sym") }}';
            let chargePercentage = '{{ gs('percent_transfer_charge') }}';
            let chargeFixed = '{{ gs('fixed_transfer_charge') }}';
            let minTrsLimit = Number("{{ gs('minimum_transfer_limit') }}");
            let dailyTrsLimit = Number("{{ gs('daily_transfer_limit') }}");
            let monthlyTrsLimit = Number("{{ gs('monthly_transfer_limit') }}");


            $("select[name=wallet_id]").on('change', function() {
                currency = $(this).find('option:selected').data('currency');
                symbol = $(this).find('option:selected').data('symbol');
                let rate = $(this).find('option:selected').data('rate');
                let totalCharge = parseFloat(rate) * chargeFixed;
                let charge = transferCharge(chargePercentage, totalCharge, symbol);
                let convertedMinTrsLimit = minTrsLimit * rate;
                let convertedDailyTrsLimit = dailyTrsLimit * rate;
                let convertedMonthlyTrsLimit = monthlyTrsLimit * rate

                $('.transCharge').text(charge);
                $('.currency').text(currency);
                $('.limitPerTrans').text(`${formatCurrency(convertedMinTrsLimit)}`);
                $('.dailyLimit').text(`${formatCurrency(convertedDailyTrsLimit)}`);
                $('.monthlyLimit').text(`${formatCurrency(convertedMonthlyTrsLimit)}`);
            }).change();

            function transferCharge(percentTransferCharge, fixedTransferCharge, curSym) {
                let charge = '';

                if (percentTransferCharge > 0) {
                    charge += `${percentTransferCharge}%`;
                }

                if (percentTransferCharge > 0 && fixedTransferCharge > 0) {
                    charge += ' + ';
                }

                if (fixedTransferCharge > 0) {
                    charge += `${formatCurrency(fixedTransferCharge)}`;
                }

                return charge;
            }

            function formatCurrency(amount) {
                const currencyFormat = "{{ gs('currency_format') }}";
                const formattedAmount = amount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                if (currencyFormat == "{{ Status::CUR_BOTH }}") {
                    return symbol + formattedAmount + ' ' + currency;
                } else if (currencyFormat == "{{ Status::CUR_TEXT }}") {
                    return formattedAmount + ' ' + currency;
                } else {
                    return symbol + formattedAmount;
                }
            }


            $('.sendBtn').on('click', function() {
                let modal = $('#sendModal');
                let route = `{{ route('user.transfer.own.bank.request', ':id') }}`;
                modal.find('form')[0].action = route.replace(':id', $(this).data('id'))
                modal.modal('show');
            });

        })(jQuery)
    </script>
@endpush
