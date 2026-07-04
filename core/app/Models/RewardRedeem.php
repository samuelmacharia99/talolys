<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RewardRedeem extends Model
{
    public function rewardPointRedeem() {
        return $this->belongsTo(RewardPointRedeem::class);
    }
}
