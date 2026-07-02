<?php

namespace App\Console\Commands;

use App\Services\Tenancy\DomainVerificationService;
use Illuminate\Console\Command;

class VerifyTenantDomainsCommand extends Command
{
    protected $signature = 'tenants:verify-domains';

    protected $description = 'Verify pending custom tenant domains via DNS TXT records';

    public function handle(DomainVerificationService $service): int
    {
        $count = $service->verifyAllPending();
        $this->info("Verified {$count} domain(s).");

        return self::SUCCESS;
    }
}
