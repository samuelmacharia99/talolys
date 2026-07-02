@extends('Template::layouts.master')
@section('content')
    <div class="d-flex justify-content-end align-items-center flex-wrap gap-2 mb-4">
        <div class="credit-card-wrapper__right gap-3">
            <button type="button" class="btn btn--dark btn--md" data-bs-toggle="modal" data-bs-target="#walletModal">
                <i class="las la-plus"></i> @lang('New Wallet')
            </button>
        </div>
    </div>
    <div class="row gy-4">
        <div class="col-xl-4">
            <div class="wallet-card">
                <div class="wallet-card__left">
                    <p class="wallet-card__label">@lang('Default Wallet')</p>
                    <h3 class="wallet-card__value">
                        {{ showAmount(auth()->user()->balance) }}
                    </h3>
                </div>
                <div class="wallet-card__right">
                    <a class="btn btn-sm btn--base" href="{{ route('user.deposit.index') }}">
                        <i class="las la-plus"></i>
                        @lang('Add Money')
                    </a>
                    <a class="btn btn-sm btn-outline--base" href="{{ route('user.transfer.history') }}">
                        <i class="las la-exchange-alt"></i>
                        @lang('Transfer Money')
                    </a>
                </div>
            </div>
        </div>
        @foreach ($wallets as $wallet)
            <div class="col-xl-4">
                <div class="wallet-card">
                    <div class="wallet-card__left">
                        <p class="wallet-card__label">{{ __($wallet->name) }}</p>
                        <h3 class="wallet-card__value">
                            {{ showAmount($wallet->balance, walletCurrency: $wallet->currency) }}
                        </h3>
                    </div>
                    <div class="wallet-card__right">
                        <a class="btn btn-sm btn--base"
                            href="{{ route('user.deposit.index') }}?wallet_id={{ $wallet->id }}">
                            <i class="las la-plus"></i>
                            @lang('Add Money')
                        </a>
                        <a class="btn btn-sm btn-outline--base" href="{{ route('user.transfer.history') }}">
                            <i class="las la-exchange-alt"></i>
                            @lang('Transfer Money')
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>


    <div class="modal custom--modal fade" id="walletModal" role="dialog" tabindex="-1">
        <div class="modal-dialog" role="document">
            <form action="{{ route('user.wallet.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('Add New Wallet')</h5>
                        <span class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                            <i class="las la-times"></i>
                        </span>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label">@lang('Name')</label>
                            <input type="text" name="name" class="form--control" required />
                        </div>
                        <div class="form-group">
                            <label class="form-label">@lang('Currency')</label>
                            <select name="currency_id" class="form--control select2" required>
                                <option value="">@lang('Select One')</option>
                                @foreach ($currencies as $currency)
                                    <option value="{{ $currency->id }}">{{ $currency->currency }}
                                        ({{ $currency->symbol }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--base">
                            @lang('Submit')
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection


@push('style')
    <style>
        .wallet-card {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px;
            border-radius: 8px;
            background-color: hsl(var(--white));
            box-shadow: 0 0 5px 0 #12263f08;
        }

        @media screen and (max-width: 575px) {
            .wallet-card {
                flex-direction: column;
            }
        }

        .wallet-card__left {
            flex-grow: 1;
            padding-right: 8px;
            border-right: 1px solid hsl(var(--black)/0.07);
        }

        @media screen and (max-width: 575px) {
            .wallet-card__left {
                border-right: none;
                padding-right: 0px;
            }
        }

        .wallet-card__right {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        @media screen and (max-width: 575px) {
            .wallet-card__right {
                gap: 16px;
                flex-direction: row;
            }
        }

        .wallet-card__label {
            font-size: 1rem;
            font-weight: 400;
            color: hsl(var(--black)/0.75);
            line-height: 100%;
            margin-bottom: 10px;
        }

        .wallet-card__value {
            line-height: 100%;
            color: hsl(var(--base));
        }
    </style>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            $('.wire-transfer').on('click', function(e) {
                let id = $(this).data('id');
                let modal = $('#detailsModal');
                modal.find('.loading').removeClass('d-none');
                let action = `{{ route('user.transfer.wire.details', ':id') }}`;

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
        })(jQuery);
    </script>
@endpush

@push('modal')
    <div class="modal fade custom--modal" id="detailsModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Wire Transfer Details')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <x-ajax-loader />
                </div>
            </div>
        </div>
    </div>
@endpush
