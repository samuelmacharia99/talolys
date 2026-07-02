<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    public const STATUS_ACTIVE = 'active';

    public const STATUS_SUSPENDED = 'suspended';

    public const STATUS_PENDING = 'pending';

    public const STATUS_TRIALING = 'trialing';

    protected $fillable = [
        'name',
        'slug',
        'plan_id',
        'status',
        'stripe_id',
        'pm_type',
        'pm_last_four',
        'trial_ends_at',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'trial_ends_at' => 'datetime',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function subscription(): ?Subscription
    {
        return $this->subscriptions()
            ->whereIn('stripe_status', ['active', 'trialing'])
            ->latest()
            ->first();
    }

    public function hasActiveSubscription(): bool
    {
        if ($this->status === self::STATUS_TRIALING && $this->trial_ends_at?->isFuture()) {
            return true;
        }

        return $this->subscription()?->active() ?? $this->status === self::STATUS_ACTIVE;
    }

    public function primaryDomain(): ?Domain
    {
        return $this->domains()->where('is_primary', true)->first()
            ?? $this->domains()->where('type', 'subdomain')->first();
    }

    public function isActive(): bool
    {
        return in_array($this->status, [self::STATUS_ACTIVE, self::STATUS_TRIALING], true);
    }

    public function subdomainUrl(): string
    {
        $domain = $this->domains()->where('type', 'subdomain')->first();

        return $domain ? 'https://' . $domain->domain : '';
    }
}
