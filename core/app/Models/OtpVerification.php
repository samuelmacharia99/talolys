<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;

use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model {
    use BelongsToTenant;

    public $timestamps = false;

    protected $casts = [
        'additional_data' => 'object',
        'send_at' => 'datetime',
        'used_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    public function verifiable() {
        return $this->morphTo();
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function wallet() {
        return $this->belongsTo(Wallet::class);
    }
}
