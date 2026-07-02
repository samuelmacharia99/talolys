<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model {
    use BelongsToTenant;
    public $timestamps = false;

    protected $hidden = [
        'token'
    ];
}
