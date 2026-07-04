@extends('admin.layouts.app')
@section('panel')
    @php
        $request = request();
        $tableName = 'reward_point_earnings';

        $tableConfiguration = $tableConfiguration = tableConfiguration($tableName);

        $columns = collect([
            prepareTableColumn('name', 'Name', className: '"name-column"'),
            prepareTableColumn(
                'account_level',
                'Account Level',
                '$item->accountLevel?->name ?? "---"',
                sortable: false,
            ),
            prepareTableColumn('transaction_amount', 'Transaction Amount', 'showAmount($item->transaction_amount)'),
            prepareTableColumn(
                'reward_point',
                'Reward Point',
                'showAmount($item->reward_point, 0, currencyFormat: false)',
            ),
            prepareTableColumn('max_use', 'Max Use'),
            prepareTableColumn('total_used', 'Total Used'),
            prepareTableColumn('per_user_limit', 'Per User Limit'),
            prepareTableColumn(
                'reward_type',
                'Reward Type',
                'collect($item->reward_type)->map(function ($t) {
                    return "<span class=\'badge bg--info me-1\'>" . ucfirst(rewardTypes($t)) . "</span>";
                })->implode(" ")',
                sortable: false,
                echoable: true,
                wrapColumnValue: true,
                wrapDivClass: 'reward-type-wrap',
            ),
            prepareTableColumn('status', 'Status', '$item->status_badge', echoable: true),
        ]);

        $action = [
            'name' => 'Action',
            'style' => 'dropdown',
            'show' => can('admin.reward.point.earning.status') || can('admin.reward.point.earning.edit'),
            'buttons' => [
                [
                    'name' => 'Edit',
                    'show' => 'can("admin.reward.point.earning.edit")',
                    'link' => 'route("admin.reward.point.earning.edit", $item->id)',
                    'icon' => 'la la-pencil',
                ],
                [
                    'name' => 'Disable',
                    'show' => 'can("admin.reward.point.earning.status") && $item->status',
                    'class' => 'confirmationBtn',
                    'icon' => 'la la-eye-slash',
                    'attributes' => [
                        'data-action' => 'route(\'admin.reward.point.earning.status\', $item->id)',
                        'data-question' => 'trans("Are you sure to disable this reward point earning?")',
                    ],
                ],
                [
                    'name' => 'Enable',
                    'show' => 'can("admin.reward.point.earning.status") && !$item->status',
                    'class' => 'confirmationBtn',
                    'icon' => 'la la-eye',
                    'attributes' => [
                        'data-action' => 'route(\'admin.reward.point.earning.status\', $item->id)',
                        'data-question' => 'trans("Are you sure to enable this reward point earning?")',
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




    <x-data_table.table :data="$rewards" :columns="$columns" :action="$action" :columnConfig="true" :tableName="$tableName"
        :visibleColumns="$visibleColumns" class="table-responsive--md table-responsive" />

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    @can('admin.reward.point.earning.create')
        <a href="{{ route('admin.reward.point.earning.create') }}" class="btn btn-sm btn-outline--primary">
            <i class="las la-plus"></i>@lang('Add New')
        </a>
    @endcan
@endpush

@push('style')
    <style>
        .name-column {
            min-width: 150px;
            white-space: wrap !important;
        }

        .reward-type-wrap {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        @media screen and (max-width: 991px) {
            .reward-type-wrap {
                justify-content: flex-end;
            }
        }

        .reward-type-wrap .badge {
            padding: 3px 6px;
        }
    </style>
@endpush
