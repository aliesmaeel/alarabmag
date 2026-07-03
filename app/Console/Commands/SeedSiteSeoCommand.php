<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Services\AiContentService;
use App\Services\SeoService;
use App\Support\SiteSeoDefaults;
use Illuminate\Console\Command;

class SeedSiteSeoCommand extends Command
{
    protected $signature = 'seo:seed
                            {--ai : توليد SEO بالذكاء الاصطناعي بدلاً من القيم الافتراضية}
                            {--instruction= : تعليمات إضافية للذكاء الاصطناعي}';

    protected $description = 'تعبئة إعدادات SEO لكل صفحات الموقع';

    public function handle(AiContentService $ai): int
    {
        if ($this->option('ai')) {
            if (! $ai->isConfigured()) {
                $this->error($ai->configurationMessage());

                return self::FAILURE;
            }

            $this->info('جاري توليد SEO بالذكاء الاصطناعي...');

            try {
                $settings = $ai->generateSitePagesSeo($this->option('instruction'));
            } catch (\Throwable $e) {
                $this->error('فشل التوليد: '.$e->getMessage());

                return self::FAILURE;
            }
        } else {
            $settings = SiteSeoDefaults::all();
        }

        Setting::setMany($settings);
        SeoService::forgetCache();

        $this->info('تم حفظ '.count($settings).' مفتاح SEO.');

        return self::SUCCESS;
    }
}
