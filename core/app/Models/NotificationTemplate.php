<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;

use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model {
    use BelongsToTenant;
    protected $casts = [
        'shortcodes' => 'object'
    ];
}
