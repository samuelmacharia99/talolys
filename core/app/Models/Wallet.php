<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use BelongsToTenant;
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function currency() {
        return $this->belongsTo(WalletCurrency::class);
    }
}
