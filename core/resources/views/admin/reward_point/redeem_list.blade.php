@extends('admin.layouts.app')
@section('panel')
    @php
        $request = request();
        $tableName = 'reward_point_redeems';

        $tableConfiguration = $tableConfiguration = tableConfiguration($tableName);

        $columns = collect([
            prepareTableColumn('name', 'Name', className: '"name-column"'),
            prepareTableColumn(
                'account_level',
                'Account Level',
                '$item->accountLevel?->name ?? "---"',
                sortable: false,
            ),
            prepareTableColumn(
                'redeem_point',
                'Redeem Point',
                'showAmount($item->redeem_point, 0, currencyFormat: false)',
            ),
            prepareTableColumn('redeem_amount', 'Redeem Amount', 'showAmount($item->redeem_amount)'),
            prepareTableColumn('total_used', 'Total Used'),
            prepareTableColumn(
                'redeemed_amount',
                'Redeemed Amount',
                'showAmount($item->rewardRedeemes->sum("redeem_amount"))',
            ),
            prepareTableColumn('status', 'Status', '$item->status_badge', echoable: true),
        ]);

        $action = [
            'name' => 'Action',
            'style' => 'dropdown',
            'show' => can('admin.reward.point.redeem.status') || can('admin.reward.point.redeem.edit'),
            'buttons' => [
                [
                    'name' => 'Edit',
                    'show' => 'can("admin.reward.point.redeem.store")',
                    'class' => 'cuModalBtn',
                    'icon' => 'la la-pencil',
                    'attributes' => [
                        'data-resource' => 'json_encode($item) ',
                        'data-modal_title' => 'trans("Edit Reward Point Redeem")',
                        'data-action' => 'route("admin.reward.point.redeem.store", $item->id)',
                    ],
                ],
                [
                    'name' => 'Disable',
                    'show' => 'can("admin.reward.point.redeem.status") && $item->status',
                    'class' => 'confirmationBtn',
                    'icon' => 'la la-eye-slash',
                    'attributes' => [
                        'data-action' => 'route(\'admin.reward.point.redeem.status\', $item->id)',
                        'data-question' => 'trans("Are you sure to disable this reward point redeem?")',
                    ],
                ],
                [
                    'name' => 'Enable',
                    'show' => 'can("admin.reward.point.redeem.status") && !$item->status',
                    'class' => 'confirmationBtn',
                    'icon' => 'la la-eye',
                    'attributes' => [
                        'data-action' => 'route(\'admin.reward.point.redeem.status\', $item->id)',
                        'data-question' => 'trans("Are you sure to enable this reward point redeem?")',
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




    <x-data_table.table :data="$redeems" :columns="$columns" :action="$action" :columnConfig="true" :tableName="$tableName"
        :visibleColumns="$visibleColumns" class="table-responsive--md table-responsive" />

    <x-confirmation-modal />

    <div class="modal fade" id="cuModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Add New Staff')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>

                <form action="{{ route('admin.reward.point.redeem.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Name')</label>
                            <input type="text" class="form-control" name="name" required maxlength="255">
                        </div>
                        <div class="form-group">
                            <label>@lang('Account Level')</label>
                            <select name="account_level_id" class="form-control select2">
                                <option value="">@lang('All Account Levels')</option>
                                @foreach ($accountLevels as $level)
                                    <option value="{{ $level->id }}" @selected(old('account_level_id', @$reward->account_level_id) == $level->id)>
                                        {{ $level->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('Redeem Point')</label>
                            <input type="number" class="form-control" name="redeem_point" value="" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Redeem Amount')</label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="redeem_amount" step="any" required>
                                <span class="input-group-text">{{ __(gs('cur_text')) }}</span>
                            </div>
                        </div>
                    </div>
                    @can('admin.reward.point.redeem.store')
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
    @can('admin.reward.point.redeem.store')
        <a class="btn btn-sm btn-outline--primary cuModalBtn" data-modal_title="@lang('New Reward Point Redeem')">
            <i class="las la-plus"></i>@lang('Add New')
        </a>
    @endcan
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/cu-modal.js') }}"></script>
@endpush

@push('style')
    <style>
        .name-column {
            min-width: 150px;
            white-space: wrap !important;
        }
    </style>
@endpush
