<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'price',
        'stripe_price_id',
        'max_users',
        'max_branches',
        'enabled_modules',
        'is_active',
    ];

    protected $casts = [
        'enabled_modules' => 'object',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }
}
