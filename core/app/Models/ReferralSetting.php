<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;

use Illuminate\Database\Eloquent\Model;

class ReferralSetting extends Model {
    use BelongsToTenant;
    protected $timestamp = false;
}
