<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;

use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model {
    use BelongsToTenant;

    protected $guarded = [];

    protected $casts = [
        'mail_config'           => 'object',
        'sms_config'            => 'object',
        'global_shortcodes'     => 'object',
        'modules'               => 'object',
        'socialite_credentials' => 'object',
        'firebase_config'       => 'object',
        'airtime_config'        => 'object',
        'branding_config'       => 'object',
        'config_progress'       => 'object',
    ];

    protected $hidden = ['email_template', 'mail_config', 'sms_config', 'system_info'];

    public function scopeSiteName($query, $pageTitle) {
        $pageTitle = empty($pageTitle) ? '' : ' - ' . $pageTitle;
        return $this->site_name . $pageTitle;
    }

    // Accessor
    public function transferCharge() {
        $charge = '';

        if ($this->percent_transfer_charge > 0) {
            $charge .= getAmount($this->percent_transfer_charge) . '%';
        }

        if ($this->percent_transfer_charge > 0 && $this->fixed_transfer_charge > 0) {
            $charge .= ' + ';
        }

        if ($this->fixed_transfer_charge > 0) {
            $charge .= $this->cur_sym . showAmount($this->fixed_transfer_charge, currencyFormat: false);
        }

        return $charge;
    }

    protected static function boot() {
        parent::boot();
        static::saved(function () {
            $context = app(\App\Support\Tenancy\TenantContext::class);
            if ($context->has()) {
                \Cache::forget($context->cacheKey('GeneralSetting'));
            } else {
                \Cache::forget('GeneralSetting');
            }
        });
    }
}
