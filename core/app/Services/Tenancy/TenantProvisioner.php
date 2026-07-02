<?php

namespace App\Services\Tenancy;

use App\Models\Domain;
use App\Models\GeneralSetting;
use App\Models\PlatformAuditLog;
use App\Models\Plan;
use App\Models\Tenant;
use App\Models\Admin;
use App\Support\Tenancy\TenantContext;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantProvisioner
{
    public function __construct(
        protected TenantContext $context
    ) {}

    public function provision(array $data, ?int $platformAdminId = null): Tenant
    {
        return DB::transaction(function () use ($data, $platformAdminId) {
            $plan = Plan::query()->where('slug', $data['plan_slug'] ?? 'starter')->first()
                ?? Plan::query()->where('is_active', true)->first();

        $tenant = Tenant::create([
            'name'   => $data['name'],
            'slug'   => $data['slug'],
            'plan_id' => $plan?->id,
            'status' => $data['status'] ?? Tenant::STATUS_ACTIVE,
            'trial_ends_at' => ($data['status'] ?? null) === Tenant::STATUS_TRIALING
                ? now()->addDays(14)
                : ($data['trial_ends_at'] ?? null),
        ]);

            $rootDomain = config('tenancy.tenant_root_domain');
            $subdomain = strtolower($data['slug']) . '.' . $rootDomain;

            Domain::create([
                'tenant_id'  => $tenant->id,
                'domain'     => $subdomain,
                'type'       => Domain::TYPE_SUBDOMAIN,
                'is_primary' => true,
                'verified_at' => now(),
            ]);

            $this->context->run($tenant, function () use ($data, $tenant) {
                $this->seedGeneralSettings($tenant, $data);
                $this->seedDefaultAdmin($data);
                \App\Models\TenantAuditLog::create([
                    'tenant_id' => $tenant->id,
                    'action' => 'tenant.provisioned',
                    'details' => json_encode(['slug' => $tenant->slug]),
                ]);
            });

            PlatformAuditLog::create([
                'platform_admin_id' => $platformAdminId,
                'tenant_id'         => $tenant->id,
                'action'            => 'tenant.provisioned',
                'ip_address'        => request()?->ip(),
                'details'           => json_encode(['slug' => $tenant->slug]),
            ]);

            return $tenant->fresh(['domains', 'plan']);
        });
    }

    protected function seedGeneralSettings(Tenant $tenant, array $data): void
    {
        GeneralSetting::create(array_merge($this->defaultGeneralSettings(), [
            'tenant_id'       => $tenant->id,
            'site_name'       => $data['name'],
            'cur_text'        => $data['currency'] ?? 'USD',
            'cur_sym'         => $data['currency_symbol'] ?? '$',
            'modules'         => $this->defaultModules(),
        ]));
    }

    protected function defaultGeneralSettings(): array
    {
        return [
            'email_from'                  => 'no-reply@talolys.com',
            'kv'                          => 0,
            'ev'                          => 0,
            'en'                          => 1,
            'sv'                          => 0,
            'sn'                          => 0,
            'pn'                          => 0,
            'force_ssl'                   => 0,
            'in_app_payment'              => 0,
            'maintenance_mode'            => 0,
            'secure_password'             => 0,
            'agree'                       => 0,
            'multi_language'              => 1,
            'registration'                => 1,
            'active_template'             => 'crystal_sky',
            'system_customized'           => 0,
            'paginate_number'             => 15,
            'currency_format'             => 3,
            'config_progress'             => '[]',
            'account_no_prefix'           => 'TL',
            'account_no_length'           => 15,
            'otp_time'                    => 120,
            'daily_transfer_limit'        => 15000,
            'monthly_transfer_limit'      => 60000,
            'minimum_transfer_limit'      => 2,
            'fixed_transfer_charge'       => 2,
            'percent_transfer_charge'     => 3,
            'referral_commission_count'   => 5,
            'statement_fee'               => 0,
            'card_issue_percent_fee'      => 0,
            'spending_limit'              => 0,
            'auto_active_card'            => 0,
            'automatic_currency_rate_update' => 0,
            'currency_exchange_rate'      => 0,
        ];
    }

    protected function seedDefaultAdmin(array $data): void
    {
        if (empty($data['admin_username']) || empty($data['admin_password'])) {
            return;
        }

        Admin::unguarded(function () use ($data) {
            Admin::create([
                'role_id'  => 0,
                'name'     => $data['admin_name'] ?? 'Bank Admin',
                'email'    => $data['admin_email'] ?? ($data['admin_username'] . '@example.com'),
                'username' => $data['admin_username'],
                'password' => Hash::make($data['admin_password']),
                'status'   => 1,
            ]);
        });
    }

    protected function defaultModules(): object
    {
        return (object) [
            'deposit' => 1,
            'withdraw' => 1,
            'dps' => 1,
            'fdr' => 1,
            'loan' => 1,
            'own_bank' => 1,
            'other_bank' => 1,
            'otp_email' => 1,
            'otp_sms' => 1,
            'branch_create_user' => 1,
            'wire_transfer' => 1,
            'referral_system' => 1,
            'airtime' => 1,
            'virtual_card' => 1,
            'wallet' => 0,
            'account_level' => 0,
            'reward_point' => 0,
        ];
    }
}
