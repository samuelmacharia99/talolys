<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;

use Illuminate\Database\Eloquent\Model;

class UserLogin extends Model {
    use BelongsToTenant;
    public function user() {
        return $this->belongsTo(User::class);
    }
}
