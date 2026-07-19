@extends('branch_staff.layouts.app')
@section('panel')
    <div class="row gy-4">
        <div class="col-xl-4">
            <div class="card b-radius--10 mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Customer')</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>@lang('Name')</span>
                            <strong>{{ $user->fullname }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>@lang('Account')</span>
                            <strong>{{ $user->account_number }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>@lang('Mobile')</span>
                            <strong>{{ $user->mobileNumber }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>@lang('Balance')</span>
                            <strong>{{ showAmount($user->balance) }}</strong>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card b-radius--10">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Loan Summary')</h5>
                </div>
                <div class="card-body">
                    @php
                        $perInstallment = $amount * $plan->per_installment / 100;
                        $delayCharge = $plan->fixed_charge + ($perInstallment * $plan->percent_charge / 100);
                    @endphp
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>@lang('Plan')</span>
                            <strong>{{ __($plan->name) }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>@lang('Loan Amount')</span>
                            <strong>{{ showAmount($amount) }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>@lang('Per Installment')</span>
                            <strong>{{ showAmount($perInstallment) }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>@lang('Total Installments')</span>
                            <strong>{{ $plan->total_installment }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0 text--danger">
                            <span>@lang('Total Payable')</span>
                            <strong>{{ showAmount($perInstallment * $plan->total_installment) }}</strong>
                        </li>
                    </ul>
                    @if ($plan->delay_value && getAmount($delayCharge))
                        <p class="mt-3 mb-0">
                            <small class="text--danger">*
                                @lang('If an installment is delayed for')
                                <strong>{{ $plan->delay_value }}</strong> @lang('or more days then')
                                <strong>{{ showAmount($delayCharge) }}</strong> @lang('will be applied for each day.')
                            </small>
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card b-radius--10">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Application Form')</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('staff.loan.apply.confirm') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @if ($plan->instruction)
                            <div class="alert alert-primary">
                                @php echo $plan->instruction @endphp
                            </div>
                        @endif
                        <x-app-form identifier="id" identifierValue="{{ $plan->form_id }}" />
                        <button type="submit" class="btn btn--primary w-100 h-45 mt-3">@lang('Submit Loan Application')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
