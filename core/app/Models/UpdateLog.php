<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;

use Illuminate\Database\Eloquent\Model;

class UpdateLog extends Model {
    use BelongsToTenant;
    protected $casts = ['update_log' => 'object'];
}
