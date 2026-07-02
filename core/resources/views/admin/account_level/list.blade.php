@extends('admin.layouts.app')
@section('panel')
    @php
        $request = request();
        $tableName = 'account_level_list';

        $tableConfiguration = $tableConfiguration = tableConfiguration($tableName);
        $columns = collect([
            prepareTableColumn(
                'name',
                'Name',
                [
                    '" <img  src=\'" . asset(getFilePath(\'accountLevel\') . "/" . $item->icon) . "\' class=\'account-info__thumb\' /> "',
                    '"<span class=\'account-info__name\'>$item->name</span>"',
                ],
                echoable: true,
                wrapColumnValue: true,
                wrapDivClass: 'account-info',
            ),
            prepareTableColumn(
                'min_transaction_amount',
                'Minimum Transaction',
                'showAmount($item->min_transaction_amount)',
            ),
            prepareTableColumn('bonus_amount', 'Bonus Amount', 'showAmount($item->bonus_amount)'),
            prepareTableColumn('status', 'Status', '$item->status_badge', echoable: true),
        ]);

        $action = [
            'name' => 'Action',
            'style' => 'dropdown',
            'show' => can('admin.account.level.store') || can('admin.account.level.status') || can('admin.users.all'),
            'buttons' => [
                [
                    'name' => 'Edit',
                    'show' => 'can("admin.account.level.store")',
                    'class' => 'cuModalBtn',
                    'icon' => 'la la-pencil',
                    'attributes' => [
                        'data-resource' => 'json_encode($item) ',
                        'data-icon' => 'asset(getFilePath(\'accountLevel\') . "/" . $item->icon)',
                        'data-modal_title' => 'trans("Update Account Level")',
                    ],
                ],
                [
                    'name' => 'Disable',
                    'show' => 'can("admin.account.level.status") && $item->status',
                    'class' => 'confirmationBtn',
                    'icon' => 'la la-eye-slash',
                    'attributes' => [
                        'data-action' => 'route(\'admin.account.level.status\', $item->id)',
                        'data-question' => 'trans("Are you sure to disable this account level?")',
                    ],
                ],
                [
                    'name' => 'Enable',
                    'show' => 'can("admin.account.level.status") && !$item->status',
                    'class' => 'confirmationBtn',
                    'icon' => 'la la-eye',
                    'attributes' => [
                        'data-action' => 'route(\'admin.account.level.status\', $item->id)',
                        'data-question' => 'trans("Are you sure to enable this account level?")',
                    ],
                ],
                [
                    'name' => 'Level Accounts',
                    'icon' => 'la la-users',
                    'show' => 'can("admin.users.all")',
                    'link' => 'route("admin.users.all") . "?account_level_id=" . $item->id',
                ],
            ],
        ];

        if ($tableConfiguration) {
            $visibleColumns = $tableConfiguration->visible_columns;
        } else {
            $visibleColumns = $columns->pluck('id')->toArray();
        }
    @endphp

    <x-viser_table.table :data="$levels" :columns="$columns" :action="$action" :columnConfig="true" :tableName="$tableName"
        :visibleColumns="$visibleColumns" class="table-responsive--md table-responsive" searchPlaceholder="Name" />

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

                <form class="account-label-form" action="{{ route('admin.account.level.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="alert-custom">
                            <h6 class="alert-custom__title">
                                <i class="fas fa-info-circle"></i>
                                @lang('Minimum Transaction Amount Requirements')
                            </h6>
                            <ul class="alert-custom-info">
                                <li class="alert-custom-info__item">
                                    The amount is cumulative, not incremental.
                                </li>
                                <li class="alert-custom-info__item">
                                    <strong>Level 1</strong>
                                    <p>Enter the starting amount (e.g., 500).</p>
                                </li>
                                <li class="alert-custom-info__item">
                                    <strong>Level 2</strong>
                                    <p>Enter the total required amount, not just the extra (e.g.,
                                        1000 = 500 +
                                        500).</p>
                                </li>
                                <li class="alert-custom-info__item">
                                    <strong>Level 3</strong> Enter the new total amount (e.g., 2000).
                                </li>
                            </ul>
                            <p class="alert-custom__note">
                                Always enter the final total amount needed to reach the level. Do not enter only the
                                additional amount
                            </p>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="iconLabel">@lang('Icon')</label>
                                    <x-image-uploader image="" name="icon" class="w-100" type="accountLevel"
                                        :required=false />
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>@lang('Name')</label>
                                    <input type="text" class="form-control" name="name" required maxlength="255">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>@lang('Minimum Transaction Amount')</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="min_transaction_amount"
                                            step="any" required>
                                        <span class="input-group-text">{{ __(gs('cur_text')) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>@lang('Bonus Amount')</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="bonus_amount" step="any">
                                        <span class="input-group-text">{{ __(gs('cur_text')) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @can('admin.account.level.store')
                        <div class="modal-footer">
                            <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                        </div>
                    @endcan
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    @can('admin.account.level.store')
        <button type="button" class="btn btn-sm btn-outline--primary cuModalBtn" data-modal_title="@lang('Add New Account Level')"
            data-icon="{{ asset(getImage(getFilePath('accountLevel') . '/' . null, getFileSize('accountLevel'))) }}">
            <i class="las la-plus"></i>@lang('Add New')
        </button>
    @endcan
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/cu-modal.js') }}"></script>
@endpush

@push('style')
    <style>
        .account-label-form .image--uploader {
            width: 100px !important;
        }

        .account-label-form .image-upload-wrapper {
            height: 100px;
        }

        .account-label-form .image-upload-input-wrapper {
            bottom: -21px
        }

        .account-label-form .image-upload-input-wrapper label {
            width: 32px;
            height: 32px;
        }

        .alert-custom {
            padding: 16px;
            border-radius: 8px;
            border: 1px solid hsl(245, 100%, 60%, 0.5);
            background-color: hsl(245, 100%, 60%, 0.05);
        }

        .alert-custom__icon {
            --size: 24px;
            width: var(--size);
            height: var(--size);
            border-radius: 50%;
            flex-shrink: 0;
            font-size: calc(var(--size) * 0.5);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: hsl(0, 0%, 100%);
            background-color: hsl(245, 100%, 60%);
        }

        .alert-custom__content {
            flex-grow: 1;
        }

        .alert-custom__title {
            font-size: 0.875rem;
            margin-bottom: 12px;
        }

        .alert-custom__title:has(i) {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .alert-custom-info__item {
            font-size: 0.875rem;
            font-weight: 400;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .alert-custom-info__item strong {
            width: 60px;
            color: #34495e;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .alert-custom-info__item strong::after {
            content: ':';
            display: inline-block;
        }

        .alert-custom-info__item p {
            flex-grow: 1;
        }

        .alert-custom-info__item:not(:last-child) {
            margin-bottom: 6px;
        }

        .alert-custom__note {
            font-size: 0.875rem;
            font-weight: 500;
            font-style: italic;
            margin-top: 8px;
        }

        .account-info {
            width: fit-content;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        @media screen and (max-width: 991px) {
            .account-info {
                margin-left: auto;
            }
        }

        .account-info__thumb {
            --size: 32px;
            width: var(--size);
            height: var(--size);
            display: block;
            border-radius: 50%;
            object-fit: cover;
        }

        .account-info__name {
            font-size: 1.125rem;
            font-weight: 500;
            color: rgb(0, 0, 0, 0.8);
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.cuModalBtn').on('click', function() {
                let icon = $(this).data('icon');
                if ($(this).data('resource')) {
                    $('#cuModal .image-upload-input').prop('required', false);
                    $('.iconLabel').removeClass('required');
                } else {
                    $('#cuModal .image-upload-input').prop('required', true);
                    $('.iconLabel').addClass('required');
                }
                $('#cuModal .image-upload-preview').css('background-image', 'url(' + icon + ')');
                console.log(icon);

            });

        })(jQuery);
    </script>
@endpush
