<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;

use Illuminate\Database\Eloquent\Model;

class RewardPoint extends Model
{
    use BelongsToTenant;
    public function rewardPointEarning() {
        return $this->belongsTo(RewardPointEarning::class);
    }
}
