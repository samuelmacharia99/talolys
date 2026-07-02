<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;

use Illuminate\Database\Eloquent\Model;

class RewardRedeem extends Model
{
    use BelongsToTenant;
    public function rewardPointRedeem() {
        return $this->belongsTo(RewardPointRedeem::class);
    }
}
