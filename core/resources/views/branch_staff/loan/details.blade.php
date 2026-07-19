@extends('branch_staff.layouts.app')
@section('panel')
    <div class="row gy-4">
        <div class="col-xl-4">
            <div class="card b-radius--10">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Loan Overview')</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>@lang('Loan Number')</span>
                            <strong>{{ $loan->loan_number }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>@lang('Status')</span>
                            <span>@php echo $loan->statusBadge @endphp</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>@lang('Plan')</span>
                            <strong>{{ __(@$loan->plan->name) }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>@lang('Amount')</span>
                            <strong>{{ showAmount($loan->amount) }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>@lang('Per Installment')</span>
                            <strong>{{ showAmount($loan->per_installment) }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>@lang('Payable')</span>
                            <strong>{{ showAmount($loan->payable_amount) }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>@lang('Given / Total')</span>
                            <strong>{{ $loan->given_installment }} / {{ $loan->total_installment }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>@lang('Applied')</span>
                            <strong>{{ showDateTime($loan->created_at) }}</strong>
                        </li>
                        @if ($loan->approved_at)
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span>@lang('Approved')</span>
                                <strong>{{ showDateTime($loan->approved_at) }}</strong>
                            </li>
                        @endif
                        @if ($loan->admin_feedback)
                            <li class="list-group-item px-0">
                                <span class="d-block text-muted mb-1">@lang('Admin Feedback')</span>
                                <strong>{{ $loan->admin_feedback }}</strong>
                            </li>
                        @endif
                    </ul>
                    <a href="{{ route('staff.loan.installments', $loan->loan_number) }}" class="btn btn-outline--primary w-100 mt-3">
                        <i class="las la-list"></i> @lang('Installments')
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card b-radius--10 mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Customer')</h5>
                </div>
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <small class="text-muted d-block">@lang('Name')</small>
                            <strong>{{ @$loan->user->fullname }}</strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">@lang('Account Number')</small>
                            <a href="{{ route('staff.account.detail', $loan->user->account_number) }}">
                                <strong>{{ @$loan->user->account_number }}</strong>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">@lang('Mobile')</small>
                            <strong>{{ @$loan->user->mobileNumber }}</strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">@lang('Originated By')</small>
                            <strong>{{ @$loan->branchStaff->name ?? '—' }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card b-radius--10">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Application Data')</h5>
                </div>
                <div class="card-body">
                    @if ($loan->application_form)
                        <div class="list-group list-group-flush">
                            @foreach ($loan->application_form as $item)
                                @continue(!@$item->value)
                                <div class="list-group-item d-flex justify-content-between flex-wrap px-0">
                                    <small class="text-muted">{{ __(@$item->name) }}</small>
                                    <span>
                                        @if (@$item->type == 'checkbox')
                                            {{ is_array($item->value) ? implode(', ', $item->value) : $item->value }}
                                        @elseif(@$item->type == 'file')
                                            <a href="{{ route('staff.download.attachment', encrypt(getFilePath('verify') . '/' . $item->value)) }}">
                                                <i class="fa fa-file"></i> @lang('View File')
                                            </a>
                                        @else
                                            <strong>{{ __($item->value) }}</strong>
                                        @endif
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">@lang('No application data')</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('staff.loan.list') }}" class="btn btn-sm btn-outline--primary">
        <i class="las la-list"></i> @lang('All Loans')
    </a>
@endpush
