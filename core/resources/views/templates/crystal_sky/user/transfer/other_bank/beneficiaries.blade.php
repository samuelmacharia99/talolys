@extends('Template::user.transfer.layout')
@section('transfer-content')
    <div class="card custom--card overflow-hidden">
        @if (gs()->modules->other_bank)
            <div class="card-header">
                <div class="header-nav mb-0">
                    <a class="btn btn-sm btn--dark" href="{{ route('user.beneficiary.other') }}"> <i class="la la-users"></i>
                        @lang('Manage Beneficiaries')</a>
                </div>
            </div>
        @endif

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table--responsive--md">
                    <thead>
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('Account Name')</th>
                            <th>@lang('Account Number')</th>
                            <th>@lang('Bank')</th>
                            <th>@lang('Supported Currency')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($beneficiaries as $beneficiary)
                            @php
                                $bank = $beneficiary->beneficiaryOf;
                            @endphp
                            <tr>
                                <td>{{ $beneficiary->short_name }}</td>
                                <td>{{ $beneficiary->account_name }}</td>
                                <td>{{ $beneficiary->account_number }}</td>
                                <td>{{ $bank->name }}</td>
                                <td>
                                    @foreach (@$bank->supported_currency ?? [] as $currency)
                                        <span class="badge badge--info">{{ __($currency) }}</span>
                                    @endforeach
                                </td>
                                <td>

                                    <div class="d-flex gap-2 justify-content-end">
                                        <button class="btn btn--sm btn-outline--base seeDetails"
                                            data-id="{{ $beneficiary->id }}"><i class="la la-desktop"></i>
                                            @lang('Details')</button>

                                        <button class="btn btn--sm btn-outline--success sendBtn"
                                            data-name="{{ $beneficiary->short_name }}"
                                            data-processing_time="{{ $bank->processing_time }}"
                                            data-fixed_charge="{{ $bank->fixed_charge }}"
                                            data-percent_charge="{{ $bank->percent_charge }}"
                                            data-bank_name="{{ $bank->name }}" data-id="{{ $beneficiary->id }}"
                                            data-minimum_amount="{{ $bank->minimum_limit }}"
                                            data-maximum_amount="{{ $bank->maximum_limit }}"
                                            data-daily_limit="{{ $bank->daily_maximum_limit }}"
                                            data-monthly_limit="{{ $bank->monthly_maximum_limit }}"
                                            data-daily_count="{{ $bank->daily_total_transaction }}"
                                            data-monthly_count="{{ $bank->monthly_total_transaction }}" type="button">
                                            <i class="las la-hand-holding-usd"></i> @lang('Transfer')
                                        </button>
                                    </div>

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
            <div class="card-footer">
                {{ paginateLinks($beneficiaries) }}
            </div>
        @endif
    </div>
@endsection

@push('modal')
    <!-- Details Modal -->
    <div class="modal fade" id="detailsModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Benficiary Details')</h5>
                    <span class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <x-ajax-loader />
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade custom--modal" id="sendModal">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Transfer Money')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="row  gx-5">
                            <div class="col-xl-5 mb-3">

                                <h6 class="mb-2 text-center">@lang('Transfer Limit')</h6>
                                <hr>
                                <ul class="caption-list-two my-3 p-0">
                                    <li class="pricing-card__list flex-between">
                                        <span class="fw-bold">@lang('Minimum Per Transaction')</span>
                                        <span class="minimum_amount"></span>
                                    </li>
                                    <li class="pricing-card__list flex-between">
                                        <span class="fw-bold">@lang('Maximum Per Transaction')</span>
                                        <span class="maximum_amount"></span>
                                    </li>
                                    <li class="pricing-card__list flex-between">
                                        <span class="fw-bold">@lang('Daily Maximum')</span>
                                        <span class="daily_limit"></span>
                                    </li>
                                    <li class="pricing-card__list flex-between">
                                        <span class="fw-bold">@lang('Monthly Maximum')</span>
                                        <span class="monthly_limit"></span>
                                    </li>
                                    <li class="pricing-card__list flex-between">
                                        <span class="fw-bold">@lang('Daily Maximum Transaction')</span>
                                        <span class="daily_count"></span>
                                    </li>
                                    <li class="pricing-card__list flex-between">
                                        <span class="fw-bold"> @lang('Monthly Maximum Transaction')</span>
                                        <span class="monthly_count"></span>
                                    </li>
                                </ul>

                                <small class="text--danger">* @lang('Processing Time'): <span
                                        class="processing_time"></span></small>
                                <div class="transfer_charge"></div>

                            </div>

                            <div class="col-xl-7">
                                <div class="form-group">
                                    <label class="required form-label">@lang('Bank')</label>
                                    <input class="bank-name form--control" class="form--control" type="text" readonly>
                                </div>
                                <div class="form-group">
                                    <label class="required form-label">@lang('Recipient')</label>
                                    <input class="short-name form--control" class="form--control" type="text" readonly>
                                </div>
                                <div class="form-group">
                                    <label class="form-label required">@lang('Wallet')</label>
                                    <select name="wallet_id" class="form-control select2">
                                        <option value="" data-currency="{{ gs('cur_text') }}"
                                            data-symbol="{{ gs('cur_sym') }}" data-rate="1">@lang('Default Wallet')
                                            ({{ showAmount(auth()->user()->balance) }})</option>
                                        @foreach ($wallets as $wallet)
                                            <option value="{{ $wallet->id }}"
                                                data-currency="{{ @$wallet->currency->currency }}"
                                                data-symbol="{{ @$wallet->currency->symbol }}"
                                                data-rate="{{ @$wallet->currency->currency_rate }}">{{ $wallet->name }}
                                                ({{ @$wallet->currency->symbol }}
                                                {{ showAmount($wallet->balance, currencyFormat: false) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="required form-label">@lang('Amount')</label>
                                    <div class="input-group custom-input-group">
                                        <input class="form-control form--control" name="amount" type="number"
                                            step="any" placeholder="@lang('Enter an Amount')" required>
                                        <span class="input-group-text currency"></span>
                                    </div>
                                </div>
                                @include('Template::partials.otp_field')
                                <button class="btn w-100 btn--base" type="submit">@lang('Submit')</button>
                            </div>
                        </div>
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
            const sendModal = $('#sendModal');
            let currency = '{{ gs('cur_text') }}';
            let symbol = '{{ gs('cur_sym') }}';
            let rate = 1;
            let data = {};

            $("select[name=wallet_id]").on('change', function() {
                currency = $(this).find('option:selected').data('currency');
                symbol = $(this).find('option:selected').data('symbol');
                rate = $(this).find('option:selected').data('rate');
                showAmount();
            });

            $('.sendBtn').on('click', function() {
                data = $(this).data();
                showAmount();

                sendModal.find('.daily_count').text(data.daily_count);
                sendModal.find('.monthly_count').text(data.monthly_count);
                sendModal.find('.bank-name').val(data.bank_name);
                sendModal.find('.short-name').val(data.name);
                sendModal.find('.processing_time').text(data.processing_time);
                sendModal.find('form')[0].action =
                    `{{ route('user.transfer.other.bank.request', '') }}/${data.id}`;
                sendModal.modal('show');
            });

            function showAmount() {
                let chargePercentage = Number(data.percent_charge);
                let chargeFixed = Number(data.fixed_charge);
                let minTrsLimit = rate * Number(data.minimum_amount);
                let maxTrsLimit = rate * Number(data.maximum_amount);
                let dailyTrsLimit = rate * Number(data.daily_limit);
                let monthlyTrsLimit = rate * Number(data.monthly_limit);


                sendModal.find('.minimum_amount').text(formatCurrency(minTrsLimit));
                sendModal.find('.maximum_amount').text(formatCurrency(maxTrsLimit));
                sendModal.find('.daily_limit').text(formatCurrency(dailyTrsLimit));
                sendModal.find('.monthly_limit').text(formatCurrency(monthlyTrsLimit));
                if (chargePercentage > 0 || chargeFixed > 0) {
                    sendModal.find('.transfer_charge').html(
                        `<small class="text--danger">* @lang('Charge'): ${transferCharge(chargePercentage, chargeFixed)}</small>`
                        );
                }
                sendModal.find('.currency').text(currency);
            }

            function transferCharge(percentTransferCharge, fixedTransferCharge) {
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

            $('.seeDetails').on('click', function() {
                let modal = $('#detailsModal');
                modal.find('.loading').removeClass('d-none');
                let action = `{{ route('user.beneficiary.details', ':id') }}`;
                let id = $(this).attr('data-id');
                $.ajax({
                    url: action.replace(':id', id),
                    type: "GET",
                    dataType: 'json',
                    cache: false,
                    success: function(response) {
                        if (response.success) {
                            modal.find('.loading').addClass('d-none');
                            modal.find('.modal-body').html(response.html);
                            modal.modal('show');
                        } else {
                            notify('error', response.message || `@lang('Something went the wrong')`)
                        }
                    },
                    error: function(e) {
                        notify(`@lang('Something went the wrong')`)
                    }
                });
            });


            function formatCurrency(amount) {
                const currencyFormat = "{{ gs('currency_format') }}";
                const formattedAmount = amount.toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
                if (currencyFormat == "{{ Status::CUR_BOTH }}") {
                    return symbol + formattedAmount + ' ' + currency;
                } else if (currencyFormat == "{{ Status::CUR_TEXT }}") {
                    return formattedAmount + ' ' + currency;
                } else {
                    return symbol + formattedAmount;
                }
            }

        })(jQuery)
    </script>
@endpush

@push('style')
    <style>
        hr {
            height: 1px;
            background-color: #dee2e6;
            opacity: 0.8;
        }
    </style>
@endpush
