<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SiteSettings
{
    /** @return array<string, string|null> */
    public static function all(): array
    {
        return Cache::remember('site_settings', 3600, fn () => Setting::getAllAsArray());
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $value = static::all()[$key] ?? null;

        return filled($value) ? $value : $default;
    }

    public static function socialUrl(string $platform): ?string
    {
        $value = static::get($platform);
        if (! filled($value)) {
            return null;
        }

        return match ($platform) {
            'instagram' => str_starts_with($value, 'http') ? $value : 'https://instagram.com/' . ltrim($value, '@'),
            'twitter' => str_starts_with($value, 'http') ? $value : 'https://x.com/' . ltrim($value, '@'),
            'youtube' => str_starts_with($value, 'http') ? $value : 'https://youtube.com/' . ltrim($value, '@'),
            'facebook' => str_starts_with($value, 'http') ? $value : 'https://facebook.com/' . ltrim($value, '@'),
            'tiktok' => str_starts_with($value, 'http') ? $value : 'https://tiktok.com/@' . ltrim($value, '@'),
            default => null,
        };
    }

    /** @return list<array{platform: string, label: string, url: string}> */
    public static function socialLinks(): array
    {
        $links = [];
        foreach ([
            'twitter' => '𝕏',
            'instagram' => '📷',
            'youtube' => '▶',
            'facebook' => '📘',
            'tiktok' => '♪',
        ] as $platform => $label) {
            if ($url = static::socialUrl($platform)) {
                $links[] = ['platform' => $platform, 'label' => $label, 'url' => $url];
            }
        }

        return $links;
    }
}
