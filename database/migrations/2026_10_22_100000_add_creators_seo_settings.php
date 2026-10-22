<?php

use App\Models\Setting;
use App\Support\SiteSeoDefaults;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $page = SiteSeoDefaults::pageMeta()['creators'];

        Setting::setMany([
            'seo_creators_title' => $page['title'],
            'seo_creators_description' => $page['description'],
            'seo_creators_keywords' => $page['keywords'],
            'og_creators_title' => $page['title'],
            'og_creators_description' => $page['description'],
        ]);
    }

    public function down(): void
    {
        //
    }
};
