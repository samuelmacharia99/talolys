<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;

use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    use BelongsToTenant;
    public function user()
    {
    	return $this->belongsTo(User::class);
    }
}
