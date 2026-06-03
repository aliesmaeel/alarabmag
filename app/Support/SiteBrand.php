<?php

namespace App\Support;

class SiteBrand
{
    public const NAME_AR = 'مجلة العرب';

    public const NAME_EN = 'Al Arab Magazine';

    public const NAME_EN_ALT = 'AL ARAB';

    public const KEYWORDS = 'مجلة العرب, Al Arab Magazine, alarab magazine, مجلة عربية, أخبار عربية, مؤثرون عرب, فنانون عرب, أطباء عرب';

    public static function homeTitle(): string
    {
        return self::NAME_AR . ' | ' . self::NAME_EN . ' — المجلة العربية للإنسان المتميّز';
    }

    public static function defaultTitle(): string
    {
        return self::NAME_AR . ' | ' . self::NAME_EN;
    }

    public static function defaultDescription(): string
    {
        return 'مجلة العرب (Al Arab Magazine) — المجلة العربية الأولى التي تحتفي بالإنسان العربي المتميّز: أخبار، مدونات، مؤثرون، فنانون، أطباء، ورواد أعمال.';
    }

    public static function homeDescription(): string
    {
        return 'مجلة العرب Al Arab Magazine — اكتشف قصص المؤثرين والفنانين والأطباء ورواد الأعمال العرب. المجلة العربية الأولى للإنسان المتميّز، من دبي للعالم العربي.';
    }

    /** @return list<string> */
    public static function alternateNames(): array
    {
        return [
            self::NAME_AR,
            self::NAME_EN,
            self::NAME_EN_ALT,
            'Alarab Magazine',
            'alarab magazine',
            'مجلة العرب الإلكترونية',
        ];
    }
}
