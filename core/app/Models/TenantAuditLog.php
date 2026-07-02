<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class TenantAuditLog extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'actor_id',
        'actor_type',
        'action',
        'ip_address',
        'details',
    ];
}
