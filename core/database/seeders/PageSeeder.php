<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $template = 'templates.crystal_sky.';

        $pages = [
            [
                'name'       => 'Home',
                'slug'       => '/',
                'secs'       => json_encode([
                    'about', 'service', 'why_choose', 'feature', 'how_it_work',
                    'dps_plans', 'fdr_plans', 'loan_plans', 'testimonial',
                    'faq', 'partner_section', 'counter',
                ]),
                'is_default' => 1,
            ],
            [
                'name'       => 'About',
                'slug'       => 'about',
                'secs'       => json_encode(['about', 'feature', 'how_it_work']),
                'is_default' => 0,
            ],
            [
                'name'       => 'FAQ',
                'slug'       => 'faq',
                'secs'       => json_encode(['faq']),
                'is_default' => 0,
            ],
            [
                'name'       => 'Contact',
                'slug'       => 'contact',
                'secs'       => null,
                'is_default' => 1,
            ],
            [
                'name'       => 'Branch',
                'slug'       => 'branch',
                'secs'       => null,
                'is_default' => 1,
            ],
        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(
                ['tempname' => $template, 'slug' => $page['slug']],
                array_merge($page, ['tempname' => $template])
            );
        }

        $this->command->info('CMS pages seeded for crystal_sky.');
    }
}
