<?php

namespace App\Filament\Support;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class SeoFields
{
    public static function section(string $contentType = 'article'): Section
    {
        return Section::make('تحسين محركات البحث (SEO) و Open Graph')
            ->description('اترك الحقول فارغة لاستخدام العنوان والوصف والصورة الافتراضية. زر ✨ يولّد المحتوى بالذكاء الاصطناعي (مجاني).')
            ->headerActions([
                AiAssist::fillAllSeoAction($contentType),
            ])
            ->schema([
                AiAssist::apply(
                    TextInput::make('meta_title')
                        ->label('عنوان SEO (meta title)')
                        ->maxLength(500)
                        ->columnSpanFull(),
                    'meta_title',
                    $contentType
                ),
                AiAssist::apply(
                    Textarea::make('meta_description')
                        ->label('وصف SEO (meta description)')
                        ->rows(2)
                        ->maxLength(500)
                        ->columnSpanFull(),
                    'meta_description',
                    $contentType
                ),
                self::keywordsWithAi($contentType),
                AiAssist::apply(
                    TextInput::make('og_title')
                        ->label('عنوان Open Graph (og:title)')
                        ->maxLength(500)
                        ->columnSpanFull(),
                    'og_title',
                    $contentType
                ),
                AiAssist::apply(
                    Textarea::make('og_description')
                        ->label('وصف Open Graph (og:description)')
                        ->rows(2)
                        ->maxLength(500)
                        ->columnSpanFull(),
                    'og_description',
                    $contentType
                ),
                ImageUpload::make('og_image', 'صورة Open Graph (og:image)')
                    ->helperText('إن تُركت فارغة تُستخدم صورة الغلاف/الشخصية.')
                    ->columnSpanFull(),
            ])
            ->collapsed()
            ->columns(1);
    }

    protected static function keywordsWithAi(string $contentType): TagsInput
    {
        return KeywordsInput::make('meta_keywords', 'كلمات مفتاحية')
            ->suffixAction(AiAssist::suffixAction('meta_keywords', $contentType))
            ->columnSpanFull();
    }
}
