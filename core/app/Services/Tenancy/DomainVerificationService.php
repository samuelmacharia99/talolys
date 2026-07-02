<?php

namespace App\Services\Tenancy;

use App\Models\Domain;
use App\Services\Tenancy\TenantResolver;
use Illuminate\Support\Str;

class DomainVerificationService
{
    public function __construct(
        protected TenantResolver $resolver
    ) {}

    public function createCustomDomain(int $tenantId, string $domain): Domain
    {
        $domain = strtolower(trim($domain));

        return Domain::create([
            'tenant_id'           => $tenantId,
            'domain'              => $domain,
            'type'                => Domain::TYPE_CUSTOM,
            'is_primary'          => false,
            'verification_token'  => Str::random(64),
        ]);
    }

    public function verificationHost(Domain $domain): string
    {
        return config('tenancy.domain_verification_prefix') . '.' . $domain->domain;
    }

    public function verify(Domain $domain): bool
    {
        if ($domain->type === Domain::TYPE_SUBDOMAIN) {
            return true;
        }

        $records = @dns_get_record($this->verificationHost($domain), DNS_TXT);

        if (!$records) {
            return false;
        }

        foreach ($records as $record) {
            if (isset($record['txt']) && trim($record['txt']) === $domain->verification_token) {
                $domain->verified_at = now();
                $domain->save();
                $this->resolver->forgetHostCache($domain->domain);

                return true;
            }
        }

        return false;
    }

    public function verifyAllPending(): int
    {
        $verified = 0;

        Domain::query()
            ->where('type', Domain::TYPE_CUSTOM)
            ->whereNull('verified_at')
            ->each(function (Domain $domain) use (&$verified) {
                if ($this->verify($domain)) {
                    $verified++;
                }
            });

        return $verified;
    }
}
