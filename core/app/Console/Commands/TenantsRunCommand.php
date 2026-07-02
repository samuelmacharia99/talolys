<?php

namespace App\Console\Commands;

use App\Http\Controllers\CronController;
use App\Models\Tenant;
use App\Support\Tenancy\TenantContext;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class TenantsRunCommand extends Command
{
    protected $signature = 'tenants:run {task=cron : Task to run for each tenant}';

    protected $description = 'Run a task in isolated context for every active tenant';

    public function handle(TenantContext $context): int
    {
        $task = $this->argument('task');

        Tenant::query()->whereIn('status', [Tenant::STATUS_ACTIVE, Tenant::STATUS_TRIALING])->each(function (Tenant $tenant) use ($context, $task) {
            $context->run($tenant, function () use ($task) {
                if ($task === 'cron') {
                    app(CronController::class)->cron(Request::create('/cron', 'GET'));
                }
            });
            $this->line("Ran {$task} for tenant #{$tenant->id} ({$tenant->slug})");
        });

        return self::SUCCESS;
    }
}
