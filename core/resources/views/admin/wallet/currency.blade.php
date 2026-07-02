@extends('admin.layouts.app')
@section('panel')
    @php
        $request = request();
        $tableName = 'wallet_list';

        $tableConfiguration = $tableConfiguration = tableConfiguration($tableName);

        $columns = collect([
            prepareTableColumn('currency', 'Currency'),
            prepareTableColumn('symbol', 'Currency Syumbol'),
            prepareTableColumn(
                'currency_rate',
                'Currency Rate',
                'showAmount($item->currency_rate, 4, walletCurrency: $item)',
            ),
            prepareTableColumn('status', 'Status', '$item->status_badge', echoable: true),
        ]);

        $action = [
            'name' => 'Action',
            'style' => 'dropdown',
            'show' => can('admin.wallet.currency.store') || can('admin.wallet.currency.status'),
            'buttons' => [
                [
                    'name' => 'Edit',
                    'show' => 'can("admin.wallet.currency.store")',
                    'class' => 'cuModalBtn',
                    'icon' => 'la la-pencil',
                    'attributes' => [
                        'data-resource' => 'json_encode($item) ',
                        'data-modal_title' => 'trans("Update Currency")',
                    ],
                ],
                [
                    'name' => 'Disable',
                    'show' => 'can("admin.wallet.currency.status") && $item->status',
                    'class' => 'confirmationBtn',
                    'icon' => 'la la-eye-slash',
                    'attributes' => [
                        'data-action' => 'route(\'admin.wallet.currency.status\', $item->id)',
                        'data-question' => 'trans("Are you sure to disable this staff?")',
                    ],
                ],
                [
                    'name' => 'Enable',
                    'show' => 'can("admin.wallet.currency.status") && !$item->status',
                    'class' => 'confirmationBtn',
                    'icon' => 'la la-eye',
                    'attributes' => [
                        'data-action' => 'route(\'admin.wallet.currency.status\', $item->id)',
                        'data-question' => 'trans("Are you sure to enable this staff?")',
                    ],
                ],
            ],
        ];

        if ($tableConfiguration) {
            $visibleColumns = $tableConfiguration->visible_columns;
        } else {
            $visibleColumns = $columns->pluck('id')->toArray();
        }
    @endphp




    <x-viser_table.table :data="$currencies" :columns="$columns" :action="$action" :columnConfig="true" :tableName="$tableName"
        :visibleColumns="$visibleColumns" class="table-responsive--md table-responsive" />

    <x-confirmation-modal />

    <!-- Create Update Modal -->
    <div class="modal fade" id="cuModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Add New Staff')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>

                <form action="{{ route('admin.wallet.currency.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Currency')</label>
                            <input type="text" class="form-control" name="currency" required maxlength="40">
                        </div>

                        <div class="form-group">
                            <label>@lang('Symbol')</label>
                            <input type="text" class="form-control" name="symbol" required maxlength="40">
                        </div>
                        <div class="form-group">
                            <div class="d-flex justify-content-between">
                                <label>
                                    @lang('Currency Rate')
                                </label>
                                <span class="float-end getCurrencyRate d-none text--primary">
                                    {{ gs('cur_text') }} @lang('To') <span class="currency text--primary"></span>
                                </span>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text">
                                    1 {{ gs('cur_text') }} =
                                </span>
                                <input type="number" class="form-control" name="currency_rate" step="any" required>
                                <span class="input-group-text currency"></span>
                            </div>
                        </div>
                    </div>
                    @can('admin.wallet.currency.store')
                        <div class="modal-footer">
                            <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                        </div>
                    @endcan
                </form>
            </div>
        </div>
    </div>

    {{-- currency api modal --}}
    <div class="modal fade" id="currencyApiModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="currencyApiModalLabel">@lang('Currency API Key')</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i></button>
                </div>
                <form action="{{ route('admin.wallet.currency.api.update') }}" method="post" class="disableSubmission">
                    @csrf
                    <div class="modal-body">
                        <div class="row form-group">
                            <div class="justify-content-between d-flex flex-wrap">
                                <label>@lang('Currency Rate API Key')</label>
                                <div>
                                    <small>@lang('For the API key') : </small>
                                    <u>
                                        <a target="_blank" class="text--primary" href="https://app.exchangerate-api.com">
                                            @lang('Exchange Rate API')
                                        </a>
                                    </u>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <input class="form-control" type="text" name="api_key" required
                                    value="{{ gs('currency_api_key') }}">
                            </div>
                        </div>
                    </div>
                    @can('admin.wallet.currency.api.update')
                        <div class="modal-footer">
                            <button type="submit" class="btn btn--primary w-100 h-45" id="btn-save" value="add">
                                @lang('Submit')
                            </button>
                        </div>
                    @endcan
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    @can('admin.wallet.currency.store')
        <button type="button" class="btn btn-sm btn-outline--primary cuModalBtn" data-modal_title="@lang('Add New Staff')">
            <i class="las la-plus"></i>@lang('Add New')
        </button>
    @endcan
    @can('admin.wallet.currency.api.update')
        <button class="btn btn-sm btn-outline--dark" data-bs-toggle="modal" data-bs-target="#currencyApiModal">
            <i class="las la-key"></i>@lang('Currency API Key')
        </button>
    @endcan
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/cu-modal.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $('input[name=currency]').on('input', function() {
                let currency = $(this).val();
                $('.currency').text(currency);
                if (currency.length > 0) {
                    $('.getCurrencyRate').removeClass('d-none');
                } else {
                    $('.getCurrencyRate').addClass('d-none');
                }
            });

            $('.cuModalBtn').on('click', function() {
                let currency = "";
                if ($(this).data('resource')) {
                    currency = $(this).data('resource').currency;
                }
                $('.currency').text(currency);
                if (currency.length > 0) {
                    $('.getCurrencyRate').removeClass('d-none');
                } else {
                    $('.getCurrencyRate').addClass('d-none');
                }
            });

            $('.getCurrencyRate').on('click', function() {
                let currency = $('input[name=currency]').val();
                if (!currency) {
                    notify('error', `@lang('Currency is required')`);
                }
                $.ajax({
                    url: "{{ route('admin.wallet.currency.rate') }}",
                    type: "post",
                    dataType: 'json',
                    data: {
                        _token: '{{ csrf_token() }}',
                        currency: currency
                    },
                    success: function(rate) {
                        if (rate) {
                            $('input[name=currency_rate]').val(rate);
                        } else {
                            notify('error', response.message || `@lang('Something went the wrong')`)
                        }
                    },
                    error: function(e) {
                        notify(`@lang('Something went the wrong')`)
                    }
                })
            });

        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .getCurrencyRate {
            cursor: pointer;
        }
    </style>
@endpush
