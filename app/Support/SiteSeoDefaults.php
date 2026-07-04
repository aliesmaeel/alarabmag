<?php

namespace App\Support;

class SiteSeoDefaults
{
    /** @return array<string, string> */
    public static function general(): array
    {
        return [
            'seo_title' => SiteBrand::defaultTitle(),
            'seo_description' => SiteBrand::defaultDescription(),
            'seo_keywords' => SiteBrand::KEYWORDS,
            'og_site_name' => SiteBrand::NAME_AR,
            'og_default_image' => '/logo.png',
            'twitter_card' => 'summary_large_image',
        ];
    }

    /** @return array<string, string> */
    public static function mainPages(): array
    {
        return [
            'home' => 'الرئيسية',
            'news' => 'الأخبار',
            'interviews' => 'المقابلات',
            'blogs' => 'المدونات',
            'doctors' => 'الأطباء',
            'influencers' => 'المؤثرون',
            'artists' => 'الفنانون',
            'business' => 'الأعمال',
            'fashion' => 'الموضة',
            'magazine' => 'المجلة',
        ];
    }

    /** @return array<string, string> */
    public static function staticPages(): array
    {
        return [
            'about' => 'عن المجلة',
            'editorial' => 'هيئة التحرير',
            'contact' => 'اتصل بنا',
            'advertise' => 'الإعلان معنا',
            'privacy' => 'سياسة الخصوصية',
            'terms' => 'شروط الاستخدام',
        ];
    }

    /** @return array<string, string> */
    public static function allPageLabels(): array
    {
        return self::mainPages() + self::staticPages();
    }

    /**
     * @return array<string, array{label: string, title: string, description: string, keywords: string}>
     */
    public static function pageMeta(): array
    {
        $brand = SiteBrand::NAME_AR;
        $en = SiteBrand::NAME_EN;
        $base = 'مجلة العرب, Al Arab Magazine, alarabmag';

        return [
            'home' => [
                'label' => 'الرئيسية',
                'title' => SiteBrand::homeTitle(),
                'description' => SiteBrand::homeDescription(),
                'keywords' => SiteBrand::KEYWORDS,
            ],
            'news' => [
                'label' => 'الأخبار',
                'title' => "الأخبار — {$brand} | {$en}",
                'description' => 'أحدث الأخبار والتحليلات من قلب العالم العربي — تغطية يومية من مجلة العرب.',
                'keywords' => "أخبار عربية, نبض العالم العربي, {$base}",
            ],
            'interviews' => [
                'label' => 'المقابلات',
                'title' => "المقابلات — حوارات تُلهم | {$brand}",
                'description' => 'مقابلات فيديو حصرية مع شخصيات عربية مؤثرة: رواد أعمال، فنانين، مؤثرين، وأطباء.',
                'keywords' => "مقابلات, حوارات, فيديو, {$base}",
            ],
            'blogs' => [
                'label' => 'المدونات',
                'title' => "المدونات — أقلام تحكي | {$brand}",
                'description' => 'آراء وتجارب وقصص من كتّاب ومبدعين عرب في مجلة العرب.',
                'keywords' => "مدونات عربية, مقالات, آراء, {$base}",
            ],
            'doctors' => [
                'label' => 'الأطباء',
                'title' => "أطباء عرب — يعالجون العالم | {$brand}",
                'description' => 'ملفات عن أطباء عرب يقودون الطب والبحث عالمياً — من مجلة العرب.',
                'keywords' => "أطباء عرب, طب, صحة, {$base}",
            ],
            'influencers' => [
                'label' => 'المؤثرون',
                'title' => "المؤثرون العرب | {$brand}",
                'description' => 'نجوم السوشيال ميديا العرب عبر الموضة والتقنية والثقافة — ملفات من مجلة العرب.',
                'keywords' => "مؤثرون عرب, سوشيال ميديا, {$base}",
            ],
            'artists' => [
                'label' => 'الفنانون',
                'title' => "الفنانون العرب | {$brand}",
                'description' => 'فنانون عرب يفرضون حضورهم على المسرح والسينما والفن — من مجلة العرب.',
                'keywords' => "فنانون عرب, فن, سينما, {$base}",
            ],
            'business' => [
                'label' => 'الأعمال',
                'title' => "الأعمال العربية — رواد يُغيّرون الاقتصاد | {$brand}",
                'description' => 'ملفات وقصص عن رجال الأعمال العرب ورواد الاقتصاد في الخليج والمشرق.',
                'keywords' => "أعمال عربية, اقتصاد, رواد أعمال, {$base}",
            ],
            'fashion' => [
                'label' => 'الموضة',
                'title' => "الموضة العربية — أناقة وإبداع | {$brand}",
                'description' => 'تقارير الموضة العربية من باريس إلى الرياض ودبي — من مجلة العرب.',
                'keywords' => "موضة عربية, أزياء, {$base}",
            ],
            'magazine' => [
                'label' => 'المجلة',
                'title' => "اقرأ المجلة — {$brand} | {$en}",
                'description' => 'تصفّح أعداد مجلة العرب الرقمية — اختر العدد واقرأ النسخة الكاملة أونلاين.',
                'keywords' => "المجلة, أعداد, قراءة, {$base}",
            ],
            'about' => [
                'label' => 'عن المجلة',
                'title' => "{$brand} — عن المجلة",
                'description' => "تعرّف على مجلة العرب ({$en}) — المجلة العربية الأولى التي تحتفي بالإنسان العربي المتميّز، صادرة من دبي للعالم العربي.",
                'keywords' => "عن المجلة, {$base}",
            ],
            'editorial' => [
                'label' => 'هيئة التحرير',
                'title' => "هيئة التحرير — {$brand}",
                'description' => 'تعرّف على فريق التحرير في مجلة العرب — صحفيون ومحررون يصوغون قصص الإنسان العربي المتميّز.',
                'keywords' => "هيئة التحرير, فريق التحرير, {$base}",
            ],
            'contact' => [
                'label' => 'اتصل بنا',
                'title' => "اتصل بـ{$brand}",
                'description' => 'تواصل مع فريق مجلة العرب — نحن هنا للاستفسارات والاقتراحات والشراكات الإعلامية.',
                'keywords' => "اتصل بنا, تواصل, {$base}",
            ],
            'advertise' => [
                'label' => 'الإعلان معنا',
                'title' => "الإعلان مع {$brand}",
                'description' => 'فرص الإعلان والشراكات مع مجلة العرب — الوصول إلى جمهور عربي متميّز.',
                'keywords' => "إعلان, شراكات, {$base}",
            ],
            'privacy' => [
                'label' => 'سياسة الخصوصية',
                'title' => "سياسة الخصوصية — {$brand}",
                'description' => 'كيف تجمع مجلة العرب وتستخدم وتحمي بياناتك عند زيارة alarabmag.com.',
                'keywords' => "سياسة الخصوصية, {$base}",
            ],
            'terms' => [
                'label' => 'شروط الاستخدام',
                'title' => "شروط الاستخدام — {$brand}",
                'description' => 'الشروط والأحكام التي تحكم استخدامك لموقع مجلة العرب.',
                'keywords' => "شروط الاستخدام, {$base}",
            ],
        ];
    }

    /** @return array<string, string> */
    public static function pageSettings(): array
    {
        $settings = [];

        foreach (self::pageMeta() as $key => $page) {
            $settings["seo_{$key}_title"] = $page['title'];
            $settings["seo_{$key}_description"] = $page['description'];
            $settings["seo_{$key}_keywords"] = $page['keywords'];
            $settings["og_{$key}_title"] = $page['title'];
            $settings["og_{$key}_description"] = $page['description'];
        }

        return $settings;
    }

    /** @return array<string, string> */
    public static function all(): array
    {
        return array_merge(self::general(), self::pageSettings());
    }

    /**
     * @return list<array{key: string, label: string, hint: string}>
     */
    public static function pagesForAi(): array
    {
        return collect(self::pageMeta())
            ->map(fn (array $page, string $key): array => [
                'key' => $key,
                'label' => $page['label'],
                'hint' => $page['description'],
            ])
            ->values()
            ->all();
    }
}
