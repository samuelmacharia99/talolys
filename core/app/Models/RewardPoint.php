<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RewardPoint extends Model
{
    public function rewardPointEarning() {
        return $this->belongsTo(RewardPointEarning::class);
    }
}
