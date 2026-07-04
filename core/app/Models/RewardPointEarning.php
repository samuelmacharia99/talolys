<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class RewardPointEarning extends Model
{
    use GlobalStatus;

    protected $casts = [
        'reward_type' => 'array',
    ];

    public function accountLevel() {
        return $this->belongsTo(AccountLevel::class);
    }
}
