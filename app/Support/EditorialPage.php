<?php

namespace App\Support;

class EditorialPage
{
    /** @return array<string, mixed> */
    public static function defaults(): array
    {
        return [
            'editorial_title' => 'هيئة التحرير',
            'editorial_lead' => 'فريق تحريري متخصص يجمع بين الصحافة العربية والتحليل العميق.',
            'editorial_team_title' => 'فريق التحرير',
            'editorial_team_body' => 'يعمل في مجلة العرب فريق تحريري متعدد التخصصات، يجمع بين صحفيين ومحللين وكتّاب عرب من مختلف أنحاء المنطقة. نلتزم بمعايير صحفية عالية ونغطي المشهد العربي بعمق وموضوعية.',
            'editorial_lead_editor_title' => 'المحررة الأولى',
            'editorial_lead_editor_name' => 'ليلى منصور',
            'editorial_lead_editor_bio' => 'محررة أولى، متخصصة في ملفات الأعمال والاقتصاد. سبق لها العمل في صحف ومجلات عربية ودولية.',
            'editorial_news_title' => 'فريق الأخبار',
            'editorial_news_team' => [
                ['name' => 'عمر الفيصل', 'role' => 'محرر أخبار وتحليلات اقتصادية وسياسية.'],
                ['name' => 'سارة خليل', 'role' => 'مراسلة أعمال وريادة أعمال.'],
                ['name' => 'زينة الخوري', 'role' => 'محررة فن وثقافة.'],
                ['name' => 'أميرة سعيد', 'role' => 'محررة موضة وثقافة معاصرة.'],
            ],
            'editorial_blogs_title' => 'المدونات والآراء',
            'editorial_blogs_body' => 'نشرف على قسم المدونات لضمان تنوع الآراء والخبرات، مع الحفاظ على معايير الجودة والاحترام. آراء الكتّاب لا تعكس بالضرورة موقف المجلة الرسمي.',
            'editorial_contact_title' => 'التواصل التحريري',
            'editorial_contact_intro' => 'للاقتراحات التحريرية أو التصحيحات:',
            'editorial_contact_email' => 'editor@alarab.com',
        ];
    }

    public static function get(string $key): string
    {
        $default = static::defaults()[$key] ?? '';

        if (is_array($default)) {
            return '';
        }

        return SiteSettings::get($key, $default) ?? $default;
    }

    /** @return list<array{name: string, role: string}> */
    public static function newsTeam(): array
    {
        $raw = SiteSettings::get('editorial_news_team');

        if (filled($raw)) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded) && $decoded !== []) {
                return array_values(array_map(
                    fn (array $member) => [
                        'name' => (string) ($member['name'] ?? ''),
                        'role' => (string) ($member['role'] ?? ''),
                    ],
                    $decoded,
                ));
            }
        }

        return static::defaults()['editorial_news_team'];
    }

    /** @return array<string, mixed> */
    public static function formData(): array
    {
        $data = [];

        foreach (static::defaults() as $key => $default) {
            $data[$key] = $key === 'editorial_news_team'
                ? static::newsTeam()
                : static::get($key);
        }

        return $data;
    }
}
