<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class WalletCurrency extends Model
{
    use BelongsToTenant;
    use GlobalStatus;
}
