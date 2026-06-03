<?php

use App\Models\Setting;
use App\Support\SiteBrand;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Setting::setMany([
            'site_name' => SiteBrand::NAME_AR,
            'site_name_en' => SiteBrand::NAME_EN,
            'seo_title' => SiteBrand::defaultTitle(),
            'seo_description' => SiteBrand::defaultDescription(),
            'seo_keywords' => SiteBrand::KEYWORDS,
            'seo_home_title' => SiteBrand::homeTitle(),
            'seo_home_description' => SiteBrand::homeDescription(),
            'og_site_name' => SiteBrand::NAME_AR,
        ]);
    }

    public function down(): void
    {
        // Brand SEO values are intentionally left in place on rollback.
    }
};
