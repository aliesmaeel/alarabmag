<?php

namespace App\Support;

use App\Models\Interview;

class HomeSections
{
    public static function hasInterviews(): bool
    {
        return Interview::query()->where('status', 'published')->exists();
    }

    /**
     * @return list<array{key: string, label: string, id: string, route?: string}>
     */
    public static function items(): array
    {
        $items = [
            ['key' => 'home', 'label' => 'الرئيسية', 'id' => 'top'],
            ['key' => 'influencers', 'label' => 'المؤثرون العرب', 'id' => 'influencers', 'route' => 'influencers.index'],
            ['key' => 'artists', 'label' => 'الفنانون العرب', 'id' => 'artists', 'route' => 'artists.index'],
            ['key' => 'business', 'label' => 'الأعمال العربية', 'id' => 'business', 'route' => 'business.index'],
            ['key' => 'doctors', 'label' => 'أطباء عرب', 'id' => 'doctors', 'route' => 'doctors.index'],
            ['key' => 'fashion', 'label' => 'الموضة العربية', 'id' => 'fashion', 'route' => 'fashion.index'],
            ['key' => 'news', 'label' => 'الأخبار', 'id' => 'news', 'route' => 'news.index'],
            ['key' => 'blogs', 'label' => 'المدونات', 'id' => 'blogs', 'route' => 'blogs.index'],
        ];

        if (static::hasInterviews()) {
            array_splice($items, 7, 0, [
                ['key' => 'interviews', 'label' => 'المقابلات', 'id' => 'interviews', 'route' => 'interviews.index'],
            ]);
        }

        return $items;
    }

    /** Header nav: on homepage, scroll to in-page sections. */
    public static function href(array $item, bool $onHome): string
    {
        if ($onHome) {
            return $item['id'] === 'top' ? '#top' : '#'.$item['id'];
        }

        if (isset($item['route'])) {
            return route($item['route']);
        }

        return url('/#'.$item['id']);
    }

    /** Sidebar: dedicated pages open full routes; homepage-only sections stay as anchors. */
    public static function sidebarHref(array $item, bool $onHome = true): string
    {
        if ($item['id'] === 'top') {
            return $onHome ? '#top' : url('/');
        }

        if (isset($item['route'])) {
            return route($item['route']);
        }

        return $onHome ? '#'.$item['id'] : url('/#'.$item['id']);
    }

    /** @return list<array{key: string, label: string, id: string, href: string, is_anchor: bool}> */
    public static function forSidebar(bool $onHome = true): array
    {
        return array_map(fn (array $item) => [
            ...$item,
            'href' => self::sidebarHref($item, $onHome),
            'is_anchor' => ! isset($item['route']),
        ], self::items());
    }
}
