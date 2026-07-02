<?php

namespace App\Models\Concerns;

use App\Models\Scopes\TenantScope;
use App\Models\Tenant;
use App\Support\Tenancy\TenantContext;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope());

        static::creating(function (Model $model) {
            /** @var TenantContext $context */
            $context = app(TenantContext::class);

            if ($context->shouldBypassScope()) {
                return;
            }

            if (!$context->has()) {
                throw new RuntimeException(sprintf(
                    'Cannot create %s without an active tenant context.',
                    static::class
                ));
            }

            if (empty($model->tenant_id)) {
                $model->tenant_id = $context->id();
            }

            if ((int) $model->tenant_id !== (int) $context->id()) {
                throw new RuntimeException('Tenant mismatch on model creation.');
            }
        });

        static::updating(function (Model $model) {
            /** @var TenantContext $context */
            $context = app(TenantContext::class);

            if ($context->shouldBypassScope() || !$context->has()) {
                return;
            }

            if ((int) $model->tenant_id !== (int) $context->id()) {
                throw new RuntimeException('Cross-tenant model update blocked.');
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
