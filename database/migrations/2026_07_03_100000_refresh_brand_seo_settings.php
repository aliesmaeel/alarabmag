<?php

use App\Models\Setting;
use App\Services\SeoService;
use App\Support\SiteSeoDefaults;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    public function up(): void
    {
        Setting::setMany(SiteSeoDefaults::all());

        Cache::forget('site_settings');
        SeoService::forgetCache();
    }

    public function down(): void
    {
        // Intentionally left in place on rollback.
    }
};
