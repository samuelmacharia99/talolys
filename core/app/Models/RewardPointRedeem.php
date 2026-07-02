<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class RewardPointRedeem extends Model
{
    use BelongsToTenant;
    use GlobalStatus;

    public function accountLevel() {
        return $this->belongsTo(AccountLevel::class);
    }

    public function rewardRedeemes()
    {
        return $this->hasMany(RewardRedeem::class);
    }
}
