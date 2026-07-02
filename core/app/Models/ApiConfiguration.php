<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;

use Illuminate\Database\Eloquent\Model;

class ApiConfiguration extends Model
{
    use BelongsToTenant;
    protected $casts = [
        'credentials' => 'object'
    ];
}
