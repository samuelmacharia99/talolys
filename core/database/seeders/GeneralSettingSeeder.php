<?php

namespace Database\Seeders;

use App\Models\GeneralSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class GeneralSettingSeeder extends Seeder
{
    public function run(): void
    {
        $tenantData = Schema::hasColumn('general_settings', 'tenant_id') ? ['tenant_id' => 1] : [];

        GeneralSetting::updateOrCreate(
            ['id' => 1],
            array_merge($tenantData, [
                'site_name'        => 'Talolys',
                'cur_text'         => 'KES',
                'cur_sym'          => 'KSh',
                'base_color'       => '4634ff',
                'secondary_color'  => '00c2ff',
                'active_template'  => 'crystal_sky',
                'mail_config'      => json_encode([
                    'name'     => 'php',
                    'host'     => '',
                    'port'     => '',
                    'enc'      => '',
                    'username' => '',
                    'password' => '',
                ]),
                'sms_config'       => json_encode(['name' => '']),
                'ev'               => 0,
                'en'               => 0,
                'sv'               => 0,
                'sn'               => 0,
                'pn'               => 0,
                'force_ssl'        => 0,
                'maintenance_mode' => 0,
                'registration'     => 1,
                'agree'            => 0,
                'multi_language'   => 1,
                'modules'          => json_encode((object)[
                    'deposit'            => 1,
                    'withdraw'           => 1,
                    'fdr'                => 1,
                    'dps'                => 1,
                    'loan'               => 1,
                    'own_bank_transfer'  => 1,
                    'other_bank_transfer'=> 1,
                    'wire_transfer'      => 1,
                    'referral_system'    => 0,
                    'virtual_card'       => 0,
                    'airtime'            => 0,
                ]),
            ])
        );

        $this->command->info('General settings seeded for Talolys.');
    }
}
