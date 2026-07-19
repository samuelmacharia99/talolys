@extends('branch_staff.layouts.app')
@section('panel')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <h5 class="mb-1">@lang('Customer')</h5>
                        <p class="mb-0 text-muted">
                            {{ $user->fullname }} ·
                            <a href="{{ route('staff.account.detail', $user->account_number) }}">{{ $user->account_number }}</a>
                            · @lang('Balance'): {{ showAmount($user->balance) }}
                        </p>
                    </div>
                    <a href="{{ route('staff.account.detail', $user->account_number) }}" class="btn btn-outline--primary btn-sm">
                        <i class="las la-user"></i> @lang('Account Details')
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 justify-content-center">
        @forelse ($plans as $plan)
            <div class="col-xxl-4 col-md-6">
                <div class="card b-radius--10 h-100">
                    <div class="card-header bg--primary">
                        <h5 class="card-title text-white mb-0">{{ __($plan->name) }}</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span>@lang('Interest / Installment')</span>
                                <strong>{{ getAmount($plan->per_installment) }}%</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span>@lang('Minimum')</span>
                                <strong>{{ showAmount($plan->minimum_amount) }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span>@lang('Maximum')</span>
                                <strong>{{ showAmount($plan->maximum_amount) }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span>@lang('Interval')</span>
                                <strong>{{ $plan->installment_interval }} {{ __(Str::plural('Day', $plan->installment_interval)) }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span>@lang('Total Installments')</span>
                                <strong>{{ $plan->total_installment }}</strong>
                            </li>
                        </ul>
                        <button type="button"
                            class="btn btn--primary w-100 mt-3 loanBtn"
                            data-id="{{ $plan->id }}"
                            data-minimum="{{ showAmount($plan->minimum_amount) }}"
                            data-maximum="{{ showAmount($plan->maximum_amount) }}">
                            @lang('Apply for Customer')
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center text-muted">
                        @lang('No active loan plans available')
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <div class="modal fade" id="loanModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('Apply for Loan')</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="las la-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="required">@lang('Amount')</label>
                            <div class="input-group">
                                <input type="number" step="any" name="amount" class="form-control" placeholder="@lang('Enter An Amount')" required>
                                <span class="input-group-text">{{ gs()->cur_text }}</span>
                            </div>
                            <p class="mb-0 mt-2"><small class="text--danger min-limit"></small></p>
                            <p class="mb-0"><small class="text--danger max-limit"></small></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Continue')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.loanBtn').on('click', function() {
                var modal = $('#loanModal');
                let data = this.dataset;
                modal.find('.min-limit').text(`Minimum Amount ${data.minimum}`);
                modal.find('.max-limit').text(`Maximum Amount ${data.maximum}`);
                modal.find('form').attr('action', `{{ url('staff/accounts/' . $user->account_number . '/loans/apply') }}/${data.id}`);
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
