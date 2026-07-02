@extends('Template::layouts.master')
@section('content')
    <div class="reward-alert mb-4">
        <div class="reward-alert__left">
            <div class="reward-alert-label">
                <img class="reward-alert-label__icon"
                    src="{{ asset(getImage(getFilePath('accountLevel') . '/' . ($accountLevel->icon ?? null), getFileSize('accountLevel'))) }}"
                    alt="">
                <span class="reward-alert-label__name">
                    {{ __($accountLevel->name ?? null) }}
                </span>
            </div>
            <div class="reward-alert-content">
                <span class="reward-alert__title">@lang('My Reward Points')</span>
                <h2 class="reward-alert__point">
                    {{ showAmount(auth()->user()->reward_point, 0, currencyFormat: false) }}
                </h2>
            </div>
        </div>
        <div class="reward-alert__right">
            <a class="btn btn--base" href="{{ route('user.account.level') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-trophy-icon lucide-trophy">
                    <path d="M10 14.66v1.626a2 2 0 0 1-.976 1.696A5 5 0 0 0 7 21.978" />
                    <path d="M14 14.66v1.626a2 2 0 0 0 .976 1.696A5 5 0 0 1 17 21.978" />
                    <path d="M18 9h1.5a1 1 0 0 0 0-5H18" />
                    <path d="M4 22h16" />
                    <path d="M6 9a6 6 0 0 0 12 0V3a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1z" />
                    <path d="M6 9H4.5a1 1 0 0 1 0-5H6" />
                </svg>
                Account Lavel
            </a>
        </div>
    </div>
    
    <div class="row gy-4">
        <div class="col-md-12">
            <div class="card custom--card h-100">
                <div class="card-header">
                    <h5 class="card-title">@lang('Reward Point Earnings')</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive ">
                        <table class="custom--table table table--responsive--md  has-search-form">
                            <thead>
                                <th>@lang('Name')</th>
                                <th>@lang('Account Level')</th>
                                <th>@lang('Expired At')</th>
                                <th>@lang('Reward Points')</th>
                            </thead>
                            <tbody>
                                @forelse ($earnings as $earning)
                                    <tr>
                                        <td>{{ __($earning->name) }}</td>
                                        <td>{{ __($earning->accountLevel->name ?? '---') }}</td>
                                        <td>{{ $earning->expired_at ? showDateTime($earning->expired_at, 'Y-m-d') : '---' }}
                                        </td>
                                        <td>{{ showAmount($earning->reward_point, 0, currencyFormat: false) }}</td>
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
                @if ($earnings->hasPages())
                    <div class="card-footer">
                        {{ paginateLinks($earnings) }}
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-12">
            <div class="card custom--card h-100">
                <div class="card-header">
                    <h5 class="card-title">@lang('Reward Point Redeems')</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive ">
                        <table class="custom--table table table--responsive--md has-search-form">
                            <thead>
                                <th>@lang('Name')</th>
                                <th>@lang('Account Level')</th>
                                <th>@lang('Redeem Point')</th>
                                <th>@lang('Redeem Amount')</th>
                                <th>@lang('Action')</th>
                            </thead>
                            <tbody>
                                @forelse ($redeems as $redeem)
                                    <tr>
                                        <td>{{ __($redeem->name) }}</td>
                                        <td>{{ __($redeem->accountLevel->name ?? '---') }}</td>
                                        <td>{{ showAmount($redeem->redeem_point, 0, currencyFormat: false) }}</td>
                                        <td>{{ showAmount($redeem->redeem_amount) }}</td>
                                        <td>
                                            @if (allowRewardRedeem($redeem, auth()->user()))
                                                <button data-question="@lang('Are you sure to redeem reward points?')"
                                                    data-action="{{ route('user.rewards.redeem', $redeem->id) }}"
                                                    class="btn btn--sm btn--base confirmationBtn">@lang('Redeem Now')</button>
                                            @else
                                                <button class="btn btn--sm btn--base" disabled>@lang('Redeem Now')</button>
                                            @endif
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
                @if ($redeems->hasPages())
                    <div class="card-footer">
                        {{ paginateLinks($redeems) }}
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-12">
            <div class="card custom--card h-100">
                <div class="card-header">
                    <h5 class="card-title">@lang('Received Reward Points')</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive ">
                        <table class="custom--table table table--responsive--md has-search-form">
                            <thead>
                                <th>@lang('Name')</th>
                                <th>@lang('Reward Points')</th>
                                <th>@lang('Date')</th>
                                <th>@lang('Details')</th>
                            </thead>
                            <tbody>
                                @forelse ($rewardPoints as $rewardPoint)
                                    <tr>
                                        <td>{{ __($rewardPoint?->rewardPointEarning?->name) }}</td>
                                        <td class="text--success">+
                                            {{ showAmount($rewardPoint->reward_point, 0, currencyFormat: false) }}</td>
                                        <td>{{ showDateTime($rewardPoint->created_at) }}</td>
                                        <td>{{ __($rewardPoint->details) }}</td>
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
                @if ($rewardPoints->hasPages())
                    <div class="card-footer">
                        {{ paginateLinks($rewardPoints) }}
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-12">
            <div class="card custom--card h-100">
                <div class="card-header">
                    <h5 class="card-title">@lang('Redeemed Reward Points')</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive ">
                        <table class="custom--table table table--responsive--md has-search-form">
                            <thead>
                                <th>@lang('Name')</th>
                                <th>@lang('Redeem Points')</th>
                                <th>@lang('Redeem Amount')</th>
                                <th>@lang('Date')</th>
                            </thead>
                            <tbody>
                                @forelse ($rewardRedeems as $rewardRedeem)
                                    <tr>
                                        <td>{{ __($rewardRedeem?->rewardPointRedeem?->name) }}</td>
                                        <td class="text--danger">-
                                            {{ showAmount($rewardRedeem->redeem_point, 0, currencyFormat: false) }}</td>
                                        <td class="text--success">+ {{ showAmount($rewardRedeem->redeem_amount) }}</td>
                                        <td>{{ showDateTime($rewardRedeem->created_at) }}</td>
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
                @if ($rewardRedeems->hasPages())
                    <div class="card-footer">
                        {{ paginateLinks($rewardRedeems) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('style')
    <style>
        .reward-alert {
            display: flex;
            align-items: center;
            border-radius: 5px;
            background-color: hsl(var(--white));
            box-shadow: var(--box-shadow);
        }

        @media screen and (max-width: 575px) {
            .reward-alert {
                align-items: flex-start;
                flex-direction: column;
            }
        }

        .reward-alert__left {
            padding: 16px 32px;
            flex-grow: 1;
        }

        @media screen and (max-width: 575px) {
            .reward-alert__left {
                width: 100%;
                padding: 12px 24px;
            }
        }

        .reward-alert__right {
            padding: 8px 24px;
            flex-shrink: 0;
            border-left: 1px solid hsl(var(--black)/0.1);
        }

        @media screen and (max-width: 575px) {
            .reward-alert__right {
                width: 100%;
                border-left: none;
                padding: 12px 16px;
            }
        }

        .reward-alert-label {
            width: fit-content;
            border-radius: 999px;
            background-color: hsl(var(--base));
            display: flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 8px;
        }

        .reward-alert-label__icon {
            --size: 32px;
            width: var(--size);
            height: var(--size);
            object-fit: cover;
            display: block;
            border-radius: 50%;
        }

        @media screen and (max-width: 575px) {
            .reward-alert-label__icon {
                --size: 24px;
            }
        }

        .reward-alert-label__name {
            font-size: 1rem;
            font-weight: 500;
            color: hsl(var(--white));
            padding: 0px 12px 0px 0px;
        }

        @media screen and (max-width: 575px) {
            .reward-alert-label__name {
                font-size: 0.875rem;
            }
        }

        .reward-alert__title {
            font-weight: 500;
            color: hsl(var(--black)/0.8);
            line-height: 100%;
            margin-bottom: 8px;
        }

        .reward-alert__point {
            color: hsl(var(--base));
            margin-bottom: 0px;
        }

        .reward-alert .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .reward-alert .btn svg {
            width: 1em;
            height: 1em;
        }
    </style>
@endpush
