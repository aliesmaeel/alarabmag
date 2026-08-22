<?php

namespace App\Support;

use App\Models\Setting;

class SiteTicker
{
    /** @return list<string> */
    public static function texts(): array
    {
        $settings = Setting::getAllAsArray();

        $raw = $settings['ticker_texts'] ?? null;
        if (filled($raw)) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $texts = static::normalize($decoded);
                if ($texts !== []) {
                    return $texts;
                }
            }
        }

        $legacy = trim((string) ($settings['ticker_text'] ?? ''));

        return $legacy !== '' ? [$legacy] : ['تابع آخر الأخبار من فريق التحرير'];
    }

    /**
     * @param  array<int|string, mixed>  $items
     * @return list<string>
     */
    public static function normalize(array $items): array
    {
        $texts = [];

        foreach ($items as $item) {
            $text = is_array($item)
                ? trim((string) ($item['text'] ?? ''))
                : trim((string) $item);

            if ($text !== '') {
                $texts[] = $text;
            }
        }

        return array_values($texts);
    }
}
