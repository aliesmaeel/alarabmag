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
            'editorial_team_title' => 'الفريق',
            'editorial_team_body' => 'يعمل في مجلة العرب فريق تحريري متعدد التخصصات، يجمع بين صحفيين ومحللين وكتّاب عرب من مختلف أنحاء المنطقة. نلتزم بمعايير صحفية عالية ونغطي المشهد العربي بعمق وموضوعية.',
            'editorial_team' => [
                [
                    'name' => 'ليلى منصور',
                    'role' => 'محررة أولى، متخصصة في ملفات الأعمال والاقتصاد. سبق لها العمل في صحف ومجلات عربية ودولية.',
                ],
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

        $value = SiteSettings::get($key, $default) ?? $default;

        // Rename legacy section title.
        if ($key === 'editorial_team_title' && $value === 'فريق التحرير') {
            return 'الفريق';
        }

        return $value;
    }

    /** @return list<array{name: string, role: string}> */
    public static function team(): array
    {
        $raw = SiteSettings::get('editorial_team');

        if (filled($raw)) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded) && $decoded !== []) {
                return static::normalizeMembers($decoded);
            }
        }

        // Migrate from the old split sections if present.
        $legacy = array_merge(static::legacyLeadEditors(), static::legacyNewsTeam());

        if ($legacy !== []) {
            return $legacy;
        }

        return static::defaults()['editorial_team'];
    }

    /** @return array<string, mixed> */
    public static function formData(): array
    {
        $data = [];

        foreach (static::defaults() as $key => $default) {
            $data[$key] = $key === 'editorial_team'
                ? static::team()
                : static::get($key);
        }

        return $data;
    }

    /** @return list<array{name: string, role: string}> */
    private static function legacyLeadEditors(): array
    {
        $raw = SiteSettings::get('editorial_lead_editors');

        if (filled($raw)) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded) && $decoded !== []) {
                return static::normalizeMembers($decoded);
            }
        }

        $name = SiteSettings::get('editorial_lead_editor_name');
        $bio = SiteSettings::get('editorial_lead_editor_bio');

        if (filled($name)) {
            return [[
                'name' => (string) $name,
                'role' => (string) ($bio ?? ''),
            ]];
        }

        return [];
    }

    /** @return list<array{name: string, role: string}> */
    private static function legacyNewsTeam(): array
    {
        $raw = SiteSettings::get('editorial_news_team');

        if (filled($raw)) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded) && $decoded !== []) {
                return static::normalizeMembers($decoded);
            }
        }

        return [];
    }

    /**
     * @param  list<array<string, mixed>>  $members
     * @return list<array{name: string, role: string}>
     */
    private static function normalizeMembers(array $members): array
    {
        return array_values(array_map(
            fn (array $member) => [
                'name' => (string) ($member['name'] ?? ''),
                'role' => (string) ($member['role'] ?? $member['bio'] ?? ''),
            ],
            $members,
        ));
    }
}
