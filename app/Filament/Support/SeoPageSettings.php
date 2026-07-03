<?php

namespace App\Filament\Support;

use App\Support\SiteSeoDefaults;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class SeoPageSettings
{
    /**
     * @return list<Section>
     */
    public static function mainPages(): array
    {
        return collect(SiteSeoDefaults::mainPages())
            ->map(fn (string $label, string $key) => self::section($key, $label, $key === 'home'))
            ->values()
            ->all();
    }

    /**
     * @return list<Section>
     */
    public static function staticPages(): array
    {
        return collect(SiteSeoDefaults::staticPages())
            ->map(fn (string $label, string $key) => self::section($key, $label))
            ->values()
            ->all();
    }

    protected static function section(string $key, string $label, bool $expanded = false): Section
    {
        $section = Section::make($label)
            ->schema([
                TextInput::make("seo_{$key}_title")
                    ->label('عنوان الصفحة (title)')
                    ->maxLength(255)
                    ->helperText($key === 'home'
                        ? 'يظهر في نتائج Google. يُفضّل أن يبدأ بـ «مجلة العرب».'
                        : null)
                    ->columnSpanFull(),
                Textarea::make("seo_{$key}_description")
                    ->label('وصف الصفحة (meta description)')
                    ->rows(2)
                    ->helperText('140–160 حرفاً. يظهر تحت العنوان في نتائج البحث.')
                    ->columnSpanFull(),
                KeywordsInput::make("seo_{$key}_keywords", 'كلمات مفتاحية')
                    ->columnSpanFull(),
                TextInput::make("og_{$key}_title")
                    ->label('og:title (اختياري)')
                    ->maxLength(255)
                    ->columnSpanFull(),
                Textarea::make("og_{$key}_description")
                    ->label('og:description (اختياري)')
                    ->rows(2)
                    ->columnSpanFull(),
                TextInput::make("og_{$key}_image")
                    ->label('og:image (رابط، اختياري)')
                    ->url()
                    ->columnSpanFull(),
            ])
            ->columns(1);

        if (! $expanded) {
            $section->collapsed();
        }

        return $section;
    }
}
