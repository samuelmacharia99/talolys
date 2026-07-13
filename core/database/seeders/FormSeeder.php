<?php

namespace Database\Seeders;

use App\Models\Form;
use Illuminate\Database\Seeder;

class FormSeeder extends Seeder
{
    public function run(): void
    {
        Form::updateOrCreate(
            ['act' => 'kyc'],
            [
                'form_data' => [
                    'full_name' => [
                        'name'        => 'Full Name',
                        'label'       => 'full_name',
                        'is_required' => 'required',
                        'instruction' => '',
                        'extensions'  => '',
                        'options'     => [],
                        'type'        => 'text',
                        'width'       => '12',
                    ],
                    'nid_number' => [
                        'name'        => 'National ID / Passport Number',
                        'label'       => 'nid_number',
                        'is_required' => 'required',
                        'instruction' => '',
                        'extensions'  => '',
                        'options'     => [],
                        'type'        => 'text',
                        'width'       => '12',
                    ],
                    'address' => [
                        'name'        => 'Address',
                        'label'       => 'address',
                        'is_required' => 'required',
                        'instruction' => '',
                        'extensions'  => '',
                        'options'     => [],
                        'type'        => 'textarea',
                        'width'       => '12',
                    ],
                    'nid_photo' => [
                        'name'        => 'ID Document Photo',
                        'label'       => 'nid_photo',
                        'is_required' => 'required',
                        'instruction' => 'Upload a clear photo of your ID document',
                        'extensions'  => 'jpg,jpeg,png,pdf',
                        'options'     => [],
                        'type'        => 'file',
                        'width'       => '12',
                    ],
                ],
            ]
        );
    }
}
