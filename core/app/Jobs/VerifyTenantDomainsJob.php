<?php

namespace App\Jobs;

use App\Services\Tenancy\DomainVerificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class VerifyTenantDomainsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(DomainVerificationService $service): void
    {
        $service->verifyAllPending();
    }
}
