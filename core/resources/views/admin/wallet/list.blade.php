@extends('admin.layouts.app')
@section('panel')

    @php
        $request = request();
        $tableName = 'wallet_list';

        $tableConfiguration = $tableConfiguration = tableConfiguration($tableName);


        $columns = collect([
            prepareTableColumn('username', 'Username', '$item->user->username', link:'route("admin.users.detail", $item->user_id)'),
            prepareTableColumn('name', 'Wallet Name', link:'route("admin.deposit.list") . "?wallet_id=" . $item->id'),
            prepareTableColumn('balance', 'Balance', 'showAmount($item->balance, walletCurrency: $item->currency)'),
        ]);

        if($tableConfiguration){
            $visibleColumns = $tableConfiguration->visible_columns;
        }else{
            $visibleColumns = $columns->pluck('id')->toArray();
        }

        $action = [
            'name' => 'Action',
            'style' => 'dropdown',
            'show' => can('admin.deposit.list'),
            'buttons' => [
                [
                    'name' => 'Wallet Deposits',
                    'icon' => 'la la-file-invoice-dollar',
                    'show' => 'can("admin.deposit.list")',
                    'link' => 'route("admin.deposit.list") . "?wallet_id=" . $item->id',
                ],
            ],
        ];
    @endphp



    <x-viser_table.table :data="$wallets" :columns="$columns" :action="$action" :tableName="$tableName" :visibleColumns="$visibleColumns" class="table-responsive--md table-responsive" />

@endsection

