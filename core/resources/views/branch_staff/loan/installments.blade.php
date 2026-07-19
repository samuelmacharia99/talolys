@extends('branch_staff.layouts.app')
@section('panel')
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex flex-wrap justify-content-between gap-2">
                    <div>
                        <h5 class="mb-1">{{ $loan->loan_number }}</h5>
                        <p class="mb-0 text-muted">{{ @$loan->user->fullname }} · {{ @$loan->user->account_number }}</p>
                    </div>
                    <a href="{{ route('staff.loan.details', $loan->loan_number) }}" class="btn btn-sm btn-outline--primary">
                        @lang('Back to Details')
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card b-radius--10">
        <div class="card-body p-0">
            <div class="table-responsive--sm table-responsive">
                <table class="table--light style--two table">
                    <thead>
                        <tr>
                            <th>@lang('#')</th>
                            <th>@lang('Installment Date')</th>
                            <th>@lang('Given At')</th>
                            <th>@lang('Delay Charge')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($installments as $installment)
                            <tr>
                                <td>{{ $loop->iteration + ($installments->currentPage() - 1) * $installments->perPage() }}</td>
                                <td>{{ showDateTime($installment->installment_date, 'd M, Y') }}</td>
                                <td>
                                    @if ($installment->given_at)
                                        {{ showDateTime($installment->given_at, 'd M, Y h:i A') }}
                                    @else
                                        <span class="text--warning">@lang('Not Paid')</span>
                                    @endif
                                </td>
                                <td>{{ showAmount($installment->delay_charge) }}</td>
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
        @if ($installments->hasPages())
            <div class="card-footer py-4">
                {{ paginateLinks($installments) }}
            </div>
        @endif
    </div>
@endsection
