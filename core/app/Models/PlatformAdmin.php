<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class PlatformAdmin extends Authenticatable
{
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'status' => 'boolean',
        'password' => 'hashed',
    ];
}
