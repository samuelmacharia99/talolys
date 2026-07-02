<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Domain extends Model
{
    public const TYPE_SUBDOMAIN = 'subdomain';

    public const TYPE_CUSTOM = 'custom';

    protected $fillable = [
        'tenant_id',
        'domain',
        'type',
        'is_primary',
        'verification_token',
        'verified_at',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function isVerified(): bool
    {
        if ($this->type === self::TYPE_SUBDOMAIN) {
            return true;
        }

        return $this->verified_at !== null;
    }

    public function isResolvable(): bool
    {
        return $this->type === self::TYPE_SUBDOMAIN || $this->isVerified();
    }
}
