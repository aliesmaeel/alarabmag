<?php

namespace App\Support;

class SiteBrand
{
    public const NAME_AR = 'مجلة العرب';

    public const NAME_EN = 'Al Arab Magazine';

    public const NAME_EN_ALT = 'AL ARAB';

    public const KEYWORDS = 'مجلة العرب, مجلة العرب الإلكترونية, Al Arab Magazine, alarabmag, alarab magazine, مجلة عربية, أخبار عربية, حوارات عربية, مؤثرون عرب, فنانون عرب, أطباء عرب, مجلة دبي';

    public static function homeTitle(): string
    {
        return 'مجلة العرب | Al Arab Magazine — المجلة العربية الأولى';
    }

    public static function defaultTitle(): string
    {
        return 'مجلة العرب | '.self::NAME_EN;
    }

    public static function defaultDescription(): string
    {
        return 'مجلة العرب (Al Arab Magazine) — المجلة العربية الأولى التي تحتفي بالإنسان العربي المتميّز: أخبار، حوارات، مؤثرون، فنانون، أطباء ورواد أعمال.';
    }

    public static function homeDescription(): string
    {
        return 'مجلة العرب — المجلة العربية الأولى للإنسان المتميّز. أخبار، مقابلات فيديو، مؤثرون، فنانون وأطباء عرب. Al Arab Magazine من دبي للعالم العربي.';
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
            'alarabmag',
            'alarabmag.com',
            'مجلة العرب الإلكترونية',
            'مجلة العرب أونلاين',
        ];
    }

    /** @return list<string> */
    public static function knowsAbout(): array
    {
        return [
            'مجلة العرب',
            'الإعلام العربي',
            'الثقافة العربية',
            'المؤثرون العرب',
            'الفنانون العرب',
            'الأطباء العرب',
            'رواد الأعمال العرب',
        ];
    }
}
