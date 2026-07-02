<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;

use Illuminate\Database\Eloquent\Model;

class AdminPasswordReset extends Model
{
    use BelongsToTenant;
    protected $table = "admin_password_resets";
    protected $guarded = ['id'];
    public $timestamps = false;
}
