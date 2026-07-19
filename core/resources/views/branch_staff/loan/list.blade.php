@extends('branch_staff.layouts.app')
@section('panel')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Loan No.')</th>
                                    <th>@lang('Account No.')</th>
                                    <th>@lang('Customer')</th>
                                    <th>@lang('Plan')</th>
                                    @if (isManager())
                                        <th>@lang('Account Officer')</th>
                                    @endif
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Applied')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($loans as $loan)
                                    <tr>
                                        <td>{{ $loan->loan_number }}</td>
                                        <td>
                                            <a href="{{ route('staff.account.detail', $loan->user->account_number) }}">
                                                {{ @$loan->user->account_number }}
                                            </a>
                                        </td>
                                        <td>{{ @$loan->user->fullname }}</td>
                                        <td>{{ __(@$loan->plan->name) }}</td>
                                        @if (isManager())
                                            <td>{{ @$loan->branchStaff->name ?? '—' }}</td>
                                        @endif
                                        <td>{{ showAmount($loan->amount) }}</td>
                                        <td>@php echo $loan->statusBadge @endphp</td>
                                        <td>{{ showDateTime($loan->created_at, 'd M Y, h:i A') }}</td>
                                        <td>
                                            <a href="{{ route('staff.loan.details', $loan->loan_number) }}" class="btn btn-sm btn-outline--primary">
                                                <i class="las la-desktop"></i> @lang('Details')
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($loans->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($loans) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form dateSearch="yes" />
    @if (authStaff()->designation == Status::ROLE_ACCOUNT_OFFICER && @gs()->modules->loan)
        <form action="{{ route('staff.account.find') }}" method="GET" class="d-flex gap-2">
            <input type="text" name="account_number" class="form-control form-control-sm" placeholder="@lang('Account / Username')" required>
            <button class="btn btn-sm btn--primary" type="submit"><i class="la la-search"></i> @lang('Find & Apply')</button>
        </form>
    @endif
@endpush
