<?php

namespace App\Filament\Support;

use Filament\Forms\Components\TagsInput;

class KeywordsInput
{
    public static function make(string $name, string $label): TagsInput
    {
        return TagsInput::make($name)
            ->label($label)
            ->placeholder('اكتب كلمة ثم اضغط Enter')
            ->helperText('أضف كلمة مفتاحية واضغط Enter، ثم كرّر للكلمات التالية.')
            ->splitKeys(['Tab', ','])
            ->reorderable()
            ->formatStateUsing(fn ($state): array => self::toArray($state))
            ->dehydrateStateUsing(fn ($state): ?string => self::toString($state));
    }

    /** @return list<string> */
    public static function toArray(mixed $state): array
    {
        if (is_array($state)) {
            return array_values(array_unique(array_filter(array_map(
                fn ($tag) => trim((string) $tag),
                $state
            ))));
        }

        if (! filled($state)) {
            return [];
        }

        return array_values(array_unique(array_filter(array_map(
            'trim',
            preg_split('/\s*,\s*/', (string) $state) ?: []
        ))));
    }

    public static function toString(mixed $state): ?string
    {
        $tags = self::toArray($state);

        return $tags !== [] ? implode(', ', $tags) : null;
    }
}
