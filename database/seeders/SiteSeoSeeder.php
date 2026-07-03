<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Services\SeoService;
use App\Support\SiteSeoDefaults;
use Illuminate\Database\Seeder;

class SiteSeoSeeder extends Seeder
{
    public function run(): void
    {
        $settings = SiteSeoDefaults::all();

        Setting::setMany($settings);
        SeoService::forgetCache();

        $this->command?->line('  → Site SEO seeded ('.count($settings).' keys)');
    }
}
