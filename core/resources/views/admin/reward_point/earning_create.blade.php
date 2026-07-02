@extends('admin.layouts.app')
@section('panel')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card box--shadow1">
                <div class="card-body">
                    <form action="{{ route('admin.reward.point.earning.store', @$reward->id) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label>@lang('Name')</label>
                                <input type="text" class="form-control" name="name"
                                    value="{{ old('name', @$reward->name) }}" required maxlength="255">
                            </div>
                            <div class="col-lg-6">
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
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Transaction Amount')</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control"
                                            value="{{ old('transaction_amount', @$reward->transaction_amount) }}"
                                            name="transaction_amount" step="any" required>
                                        <span class="input-group-text">{{ __(gs('cur_text')) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Reward Point')</label>
                                    <input type="number" class="form-control" name="reward_point"
                                        value="{{ old('reward_point', @$reward->reward_point) }}" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <div class="d-flex justify-content-between">
                                        <label>@lang('Max Use')</label>
                                        <span class="small text-italic text--info">@lang('Apply') <strong>-1</strong>
                                            @lang('for unlimited')</span>
                                    </div>
                                    <input type="number" class="form-control" name="max_use"
                                        value="{{ old('max_use', @$reward->max_use) }}" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <div class="d-flex justify-content-between">
                                        <label>@lang('Per User Limit')</label>
                                        <span class="small text-italic text--info">@lang('Apply') <strong>-1</strong>
                                            @lang('for unlimited')</span>
                                    </div>
                                    <input type="number" class="form-control" name="per_user_limit"
                                        value="{{ old('per_user_limit', @$reward->per_user_limit) }}" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Reward Type')</label>
                                    <select name="reward_type[]" class="form-control select2" multiple required>
                                        <option value="" disabled>@lang('Select Reward Types')</option>
                                        @foreach (rewardTypes() as $key => $type)
                                            <option value="{{ $key }}" @selected(in_array($key, old('reward_type', @$reward->reward_type ?? [])))>
                                                {{ __($type) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Started At')</label>
                                    <input type="date" class="form-control"
                                        value="{{ old('started_at', @$reward->started_at) }}" name="started_at">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Expired At')</label>
                                    <input type="date" class="form-control" name="expired_at"
                                        value="{{ old('expired_at', @$reward->expired_at) }}">
                                </div>
                            </div>
                            @can('admin.reward.point.earning.store')
                                <div class="col-md-12">
                                    <div class="form-group mb-0">
                                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                                    </div>
                                </div>
                            @endcan
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    @can('admin.reward.point.earning.list')
        @push('breadcrumb-plugins')
            <x-back route="{{ route('admin.reward.point.earning.list') }}" />
        @endpush
    @endcan
@endpush
