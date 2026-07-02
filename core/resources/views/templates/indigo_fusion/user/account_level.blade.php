@extends('Template::layouts.master')
@section('content')
    <div class="row justify-content-center">
        <div class="col-xxl-10">
            <div class="progression-tree">
                @foreach ($accountLevels as $accountLevel)
                    <div
                        class="tier-node {{ auth()->user()->account_level_id == $accountLevel->id ? 'current' : '' }}  {{ $totalDeposit >= $accountLevel->min_transaction_amount ? 'unlocked' : 'locked' }}">
                        <div class="tier-content">
                            <div class="tier-header">
                                <img class="tier-icon-wrapper"
                                    src="{{ asset(getImage(getFilePath('accountLevel') . '/' . $accountLevel->icon, getFileSize('accountLevel'))) }}"
                                    alt="referral">
                                <div class="tier-info">
                                    <h5>{{ __($accountLevel->name) }}</h5>
                                </div>
                                @if ($totalDeposit >= $accountLevel->min_transaction_amount)
                                    <span class="tier-badge unlocked"><i class="fas fa-check"></i>@lang('Unlocked')</span>
                                @else
                                    <span class="tier-badge locked"><i class="fas fa-check"></i>@lang('Locked')</span>
                                @endif

                            </div>
                            <div class="tier-body">
                                <div class="tier-stats">
                                    <div class="stat-item">
                                        <div class="stat-label">@lang('Bonus')</div>
                                        <div class="stat-value">{{ showAmount($accountLevel->bonus_amount) }}</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-label">@lang('Min Deposit')</div>
                                        <div class="stat-value">{{ showAmount($accountLevel->min_transaction_amount) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .progression-tree::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 4px;
            background: hsl(var(--base));
            transform: translateX(-50%);
            z-index: 0;
        }

        .progression-tree {
            position: relative;
        }

        .tier-node {
            position: relative;
            margin-bottom: 48px;
            z-index: 1;
        }

        @media screen and (max-width: 767px) {
            .tier-node {
                margin-bottom: 32px;
            }
        }

        @media screen and (max-width: 575px) {
            .tier-node {
                margin-bottom: 24px;
            }
        }

        .tier-node:last-child {
            margin-bottom: 0;
        }

        .tier-node:nth-child(odd) .tier-content {
            margin-left: auto;
            margin-right: 40px;
        }

        .tier-node:nth-child(even) .tier-content {
            margin-right: auto;
            margin-left: 60px;
        }

        .tier-node.unlocked::before {
            background: hsl(var(--base));
            box-shadow: 0 0 30px hsl(var(--base), 0.8);
        }

        .tier-content {
            width: 50%;
            background: hsl(var(--white));
            border-radius: 24px;
            border: 1px solid hsl(var(--base), 0.3);
            padding: 0;
            overflow: hidden;
            transition: all 0.4s ease;
            position: relative;
        }

        .tier-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, transparent, hsl(var(--base)), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .tier-node.locked .tier-content::after {
            content: '';
            width: 100%;
            height: 100%;
            display: inline-block;
            position: absolute;
            inset: 0;
            background-color: hsl(var(--secondary), 0.05);
            z-index: 2;
        }


        .tier-node.current .tier-content {
            transform: scale(1);
            border-color: hsl(var(--base), 0.6);
            box-shadow: 0 15px 50px hsl(var(--base), 0.3);
        }

        .tier-node:hover .tier-content {
            transform: scale(1.02);
            border-color: hsl(var(--base), 0.6);
            box-shadow: 0 10px 20px hsl(var(--base), 0.3);
        }


        .tier-node.current .tier-content::before {
            opacity: 1;
        }

        .tier-node:hover .tier-content::before {
            opacity: 1;
        }

        .tier-header {
            background: hsl(var(--base), 0.3);
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
        }

        .tier-icon-wrapper {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: block;
            object-fit: contain;
            flex-shrink: 0;
        }

        .tier-icon-wrapper i {
            font-size: 36px;
            color: white;
            z-index: 1;
        }

        .tier-icon-wrapper::before {
            content: '';
            position: absolute;
            inset: -3px;
            border-radius: inherit;
            background: hsl(var(--base));
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .tier-node.current .tier-icon-wrapper::before {
            opacity: 1;
        }

        .tier-node:hover .tier-icon-wrapper::before {
            opacity: 1;
        }


        .tier-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.625rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            line-height: 100%;
        }

        .tier-badge.unlocked {
            background: rgba(34, 197, 94, 0.2);
            color: #009966;
            border: 1px solid #009966;
        }

        .tier-badge.locked {
            background: rgba(107, 114, 128, 0.2);
            color: #9ca3af;
            border: 1px solid #6b7280;
        }

        .tier-badge.current {
            background: rgb(97, 95, 255, 0.2);
            color: #615fff;
            border: 1px solid #615fff;
        }

        .tier-body {
            padding: 16px;
        }

        .tier-stats {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        }

        .stat-item {
            background: hsl(var(--base), 0.1);
            padding: 12px;
            border-radius: 12px;
            border: 1px solid hsl(var(--base), 0.2);
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            background: hsl(var(--base), 0.2);
            transform: translateY(-3px);
        }

        .stat-label {
            color: hsl(var(--base));
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            line-height: 100%;
        }

        .stat-value {
            color: hsl(var(--secondary));
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 100%;
        }



        /* Mobile Responsive */
        @media (max-width: 767px) {

            .tier-node:nth-child(odd) .tier-content,
            .tier-node:nth-child(even) .tier-content {
                width: calc(100% - 32px);
                margin: 0 0 0 32px;
                padding: 0;
            }

            .progression-tree::before {
                left: 20px;
            }
        }

        @media (max-width: 575px) {
            .progression-tree::before {
                left: 10px;
            }

            .tier-stats {
                grid-template-columns: 1fr;
            }

            .tier-node:nth-child(odd) .tier-content,
            .tier-node:nth-child(even) .tier-content {
                width: calc(100% - 24px);
                margin: 0 0 0 24px;
                padding: 0;
            }

        }
    </style>
@endpush
