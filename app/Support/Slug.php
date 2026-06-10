<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;

class Slug
{
    /**
     * Build a URL slug that keeps Arabic letters (not Latin transliteration).
     */
    public static function fromTitle(string $title, string $fallback = ''): string
    {
        $slug = trim($title);

        // Remove tashkeel and tatweel.
        $slug = preg_replace('/[\x{064B}-\x{065F}\x{0670}\x{0640}]/u', '', $slug) ?? '';
        $slug = preg_replace('/\s+/u', '-', $slug) ?? '';
        $slug = preg_replace('/[^\p{Arabic}a-zA-Z0-9\-]+/u', '', $slug) ?? '';
        $slug = preg_replace('/-+/', '-', $slug) ?? '';
        $slug = trim($slug, '-');

        if ($slug === '') {
            return $fallback;
        }

        if (mb_strlen($slug) > 200) {
            $slug = rtrim(mb_substr($slug, 0, 200), '-');
        }

        return $slug;
    }

    /**
     * @param  class-string<Model>  $modelClass
     */
    public static function unique(string $title, string $modelClass, ?int $ignoreId = null, string $fallback = 'item'): string
    {
        $base = self::fromTitle($title);

        if ($base === '') {
            $base = $ignoreId ? "{$fallback}-{$ignoreId}" : $fallback;
        }

        $slug = $base;
        $counter = 2;

        while ($modelClass::query()
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()
        ) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
