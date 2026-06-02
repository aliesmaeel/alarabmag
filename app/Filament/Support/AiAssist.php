<?php

namespace App\Filament\Support;

use App\Services\AiContentService;
use App\Services\WebSearchService;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

class AiAssist
{
    public static function suffixAction(string $field, string $contentType): Action
    {
        return Action::make('ai_'.$field)
            ->icon('heroicon-m-sparkles')
            ->color('warning')
            ->disabled(fn (): bool => ! app(AiContentService::class)->isConfigured())
            ->tooltip(fn (): string => app(AiContentService::class)->isConfigured()
                ? 'توليد بالذكاء الاصطناعي (مجاني)'
                : app(AiContentService::class)->configurationMessage())
            ->modalHeading('توليد بالذكاء الاصطناعي')
            ->modalDescription('اكتب ما تريد (اختياري) ثم اضغط توليد. يستخدم نموذجاً مفتوح المصدر مجاناً.')
            ->modalSubmitActionLabel('توليد')
            ->form([
                Textarea::make('instruction')
                    ->label('ماذا تريد؟ (اختياري)')
                    ->placeholder('مثال: ركّز على الإمارات، أسلوب صحفي رسمي...')
                    ->rows(3),
            ])
            ->action(function (array $data, Get $get, Set $set) use ($field, $contentType): void {
                self::runFieldGeneration($field, $contentType, $get, $set, $data['instruction'] ?? null);
            });
    }

    public static function fillAllSeoAction(string $contentType): Action
    {
        return Action::make('ai_fill_seo')
            ->label('توليد كل SEO')
            ->icon('heroicon-o-sparkles')
            ->color('warning')
            ->disabled(fn (): bool => ! app(AiContentService::class)->isConfigured())
            ->modalHeading('توليد كل حقول SEO')
            ->modalDescription('يملأ meta_title و description و keywords و Open Graph دفعة واحدة.')
            ->modalSubmitActionLabel('توليد الكل')
            ->form([
                Textarea::make('instruction')
                    ->label('تعليمات إضافية (اختياري)')
                    ->rows(3),
            ])
            ->action(function (array $data, Get $get, Set $set) use ($contentType): void {
                $ai = app(AiContentService::class);

                if (! $ai->isConfigured()) {
                    Notification::make()->danger()->title('الذكاء الاصطناعي غير مفعّل')->body($ai->configurationMessage())->send();

                    return;
                }

                try {
                    $bundle = $ai->generateSeoBundle(self::context($get, $contentType), $data['instruction'] ?? null);

                    foreach ($bundle as $key => $value) {
                        $set($key, $value);
                    }

                    Notification::make()->success()->title('تم توليد حقول SEO')->send();
                } catch (\Throwable $e) {
                    Notification::make()->danger()->title('فشل التوليد')->body($e->getMessage())->send();
                }
            });
    }

    public static function generateFullArticleAction(string $contentType = 'article'): Action
    {
        $categories = [
            'عام' => 'عام',
            'سياسة' => 'سياسة',
            'اقتصاد' => 'اقتصاد',
            'أعمال' => 'أعمال',
            'رياضة' => 'رياضة',
            'ثقافة' => 'ثقافة',
            'فن' => 'فن',
            'موضة' => 'موضة',
            'تكنولوجيا' => 'تكنولوجيا',
            'صحة' => 'صحة',
        ];

        return Action::make('ai_full_article')
            ->label('توليد خبر كامل')
            ->icon('heroicon-o-globe-alt')
            ->color('success')
            ->disabled(fn (): bool => ! app(AiContentService::class)->isConfigured())
            ->tooltip(fn (): string => app(AiContentService::class)->isConfigured()
                ? 'بحث في الإنترنت + كتابة الخبر بالذكاء الاصطناعي'
                : app(AiContentService::class)->configurationMessage())
            ->modalHeading('توليد خبر كامل من الإنترنت')
            ->modalDescription(fn (): string => 'يُبحث في الويب ثم يُملأ العنوان والنص وSEO. '.app(WebSearchService::class)->configurationMessage())
            ->modalSubmitActionLabel('توليد الخبر')
            ->modalWidth('2xl')
            ->closeModalByClickingAway(false)
            ->form([
                TextInput::make('subject')
                    ->label('موضوع الخبر')
                    ->placeholder('مثال: تأثير الذكاء الاصطناعي على الاقتصاد الخليجي')
                    ->required()
                    ->maxLength(500)
                    ->columnSpanFull(),
                Textarea::make('details')
                    ->label('تفاصيل وملاحظات (اختياري)')
                    ->placeholder('من المشارك؟ زاوية الخبر؟ حقائق يجب ذكرها؟ الجمهور المستهدف؟')
                    ->rows(4)
                    ->columnSpanFull(),
                Select::make('category')
                    ->label('القسم (اختياري)')
                    ->options($categories)
                    ->native(false),
                TextInput::make('author')
                    ->label('الكاتب')
                    ->default('فريق التحرير')
                    ->maxLength(200),
                Toggle::make('use_web_search')
                    ->label('البحث في الإنترنت قبل الكتابة')
                    ->default(true)
                    ->helperText('يُجمع معلومات من مصادر حية ثم يُصاغ الخبر'),
                Textarea::make('instruction')
                    ->label('تعليمات أسلوب (اختياري)')
                    ->placeholder('مثال: أسلوب تحليلي، ركّز على السعودية، تجنب العناوين الصادمة...')
                    ->rows(2)
                    ->columnSpanFull(),
            ])
            ->action(function (array $data, Get $get, Set $set) use ($contentType): void {
                $ai = app(AiContentService::class);

                if (! $ai->isConfigured()) {
                    Notification::make()->danger()->title('الذكاء الاصطناعي غير مفعّل')->body($ai->configurationMessage())->send();

                    return;
                }

                try {
                    $generated = $ai->generateFullArticle(
                        $data['subject'],
                        $data['details'] ?? null,
                        [
                            'use_web_search' => (bool) ($data['use_web_search'] ?? true),
                            'category' => $data['category'] ?? $get('category'),
                            'author' => $data['author'] ?? $get('author'),
                            'instruction' => $data['instruction'] ?? null,
                        ]
                    );

                    $searchNote = $generated['_search_note'] ?? '';
                    unset($generated['_search_note']);

                    self::applyGeneratedFields($generated, $set);

                    Notification::make()
                        ->success()
                        ->title('تم توليد الخبر')
                        ->body($searchNote ?: 'راجع المحتوى قبل النشر.')
                        ->duration(8000)
                        ->send();
                } catch (\Throwable $e) {
                    Notification::make()->danger()->title('فشل توليد الخبر')->body($e->getMessage())->duration(10000)->send();
                }
            });
    }

    public static function fillExcerptAction(string $contentType): Action
    {
        return Action::make('ai_excerpt')
            ->label('توليد المقتطف')
            ->icon('heroicon-o-sparkles')
            ->color('warning')
            ->disabled(fn (): bool => ! app(AiContentService::class)->isConfigured())
            ->action(function (Get $get, Set $set) use ($contentType): void {
                self::runFieldGeneration('excerpt', $contentType, $get, $set, null);
            });
    }

    /**
     * @param  array<string, mixed>  $generated
     */
    protected static function applyGeneratedFields(array $generated, Set $set): void
    {
        foreach ($generated as $key => $value) {
            if (filled($value) || in_array($key, ['title', 'body'], true)) {
                $set($key, $value);
            }
        }
    }

    protected static function runFieldGeneration(string $field, string $contentType, Get $get, Set $set, ?string $instruction): void
    {
        $ai = app(AiContentService::class);

        if (! $ai->isConfigured()) {
            Notification::make()->danger()->title('الذكاء الاصطناعي غير مفعّل')->body($ai->configurationMessage())->send();

            return;
        }

        try {
            $value = $ai->generateField($field, self::context($get, $contentType), $instruction);
            $set($field, $value);

            Notification::make()->success()->title('تم التوليد')->send();
        } catch (\Throwable $e) {
            Notification::make()->danger()->title('فشل التوليد')->body($e->getMessage())->send();
        }
    }

    /**
     * @return array<string, mixed>
     */
    public static function context(Get $get, string $contentType): array
    {
        $body = $get('body') ?? $get('bio') ?? '';

        return array_filter([
            'content_type' => $contentType,
            'title' => $get('title'),
            'subtitle' => $get('subtitle'),
            'name' => $get('name'),
            'excerpt' => $get('excerpt'),
            'category' => $get('category'),
            'author' => $get('author'),
            'role' => $get('role'),
            'specialty' => $get('specialty'),
            'region' => $get('region'),
            'tags' => $get('tags'),
            'body_text' => filled($body) ? Str::limit(strip_tags((string) $body), 2500) : null,
        ], fn ($v) => filled($v));
    }

    /** @param  \Filament\Forms\Components\Field  $field */
    public static function apply($field, string $fieldName, string $contentType): mixed
    {
        if (! method_exists($field, 'suffixAction')) {
            return $field;
        }

        return $field->suffixAction(self::suffixAction($fieldName, $contentType));
    }
}
