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
                ? __('توليد بالذكاء الاصطناعي (مجاني)')
                : app(AiContentService::class)->configurationMessage())
            ->modalHeading(__('توليد بالذكاء الاصطناعي'))
            ->modalDescription(__('اكتب ما تريد (اختياري) ثم اضغط توليد. يستخدم نموذجاً مفتوح المصدر مجاناً.'))
            ->modalSubmitActionLabel(__('توليد'))
            ->form([
                Textarea::make('instruction')
                    ->label(__('ماذا تريد؟ (اختياري)'))
                    ->placeholder(__('مثال: ركّز على الإمارات، أسلوب صحفي رسمي...'))
                    ->rows(3),
            ])
            ->action(function (array $data, Get $get, Set $set) use ($field, $contentType): void {
                self::runFieldGeneration($field, $contentType, $get, $set, $data['instruction'] ?? null);
            });
    }

    public static function fillAllSeoAction(string $contentType): Action
    {
        return Action::make('ai_fill_seo')
            ->label(__('توليد كل SEO'))
            ->icon('heroicon-o-sparkles')
            ->color('warning')
            ->disabled(fn (): bool => ! app(AiContentService::class)->isConfigured())
            ->modalHeading(__('توليد كل حقول SEO'))
            ->modalDescription(__('يملأ meta_title و description و keywords و Open Graph دفعة واحدة.'))
            ->modalSubmitActionLabel(__('توليد الكل'))
            ->form([
                Textarea::make('instruction')
                    ->label(__('تعليمات إضافية (اختياري)'))
                    ->rows(3),
            ])
            ->action(function (array $data, Get $get, Set $set) use ($contentType): void {
                $ai = app(AiContentService::class);

                if (! $ai->isConfigured()) {
                    Notification::make()->danger()->title(__('الذكاء الاصطناعي غير مفعّل'))->body($ai->configurationMessage())->send();

                    return;
                }

                try {
                    $bundle = $ai->generateSeoBundle(self::context($get, $contentType), $data['instruction'] ?? null);

                    foreach ($bundle as $key => $value) {
                        $set($key, $value);
                    }

                    Notification::make()->success()->title(__('تم توليد حقول SEO'))->send();
                } catch (\Throwable $e) {
                    Notification::make()->danger()->title(__('فشل التوليد'))->body($e->getMessage())->send();
                }
            });
    }

    public static function generateFullArticleAction(string $contentType = 'article'): Action
    {
        $categories = [
            'عام' => __('عام'),
            'سياسة' => __('سياسة'),
            'اقتصاد' => __('اقتصاد'),
            'أعمال' => __('أعمال'),
            'رياضة' => __('رياضة'),
            'ثقافة' => __('ثقافة'),
            'فن' => __('فن'),
            'موضة' => __('موضة'),
            'تكنولوجيا' => __('تكنولوجيا'),
            'صحة' => __('صحة'),
        ];

        return Action::make('ai_full_article')
            ->label(__('توليد خبر كامل'))
            ->icon('heroicon-o-globe-alt')
            ->color('success')
            ->disabled(fn (): bool => ! app(AiContentService::class)->isConfigured())
            ->tooltip(fn (): string => app(AiContentService::class)->isConfigured()
                ? __('بحث في الإنترنت + كتابة الخبر بالذكاء الاصطناعي')
                : app(AiContentService::class)->configurationMessage())
            ->modalHeading(__('توليد خبر كامل من الإنترنت'))
            ->modalDescription(fn (): string => __('يُبحث في الويب ثم يُملأ العنوان والنص وSEO. ').app(WebSearchService::class)->configurationMessage())
            ->modalSubmitActionLabel(__('توليد الخبر'))
            ->modalWidth('2xl')
            ->closeModalByClickingAway(false)
            ->form([
                TextInput::make('subject')
                    ->label(__('موضوع الخبر'))
                    ->placeholder(__('مثال: تأثير الذكاء الاصطناعي على الاقتصاد الخليجي'))
                    ->required()
                    ->maxLength(500)
                    ->columnSpanFull(),
                Textarea::make('details')
                    ->label(__('تفاصيل وملاحظات (اختياري)'))
                    ->placeholder(__('من المشارك؟ زاوية الخبر؟ حقائق يجب ذكرها؟ الجمهور المستهدف؟'))
                    ->rows(4)
                    ->columnSpanFull(),
                Select::make('category')
                    ->label(__('القسم (اختياري)'))
                    ->options($categories)
                    ->native(false),
                TextInput::make('author')
                    ->label(__('الكاتب'))
                    ->default('فريق التحرير')
                    ->maxLength(200),
                Toggle::make('use_web_search')
                    ->label(__('البحث في الإنترنت قبل الكتابة'))
                    ->default(true)
                    ->helperText(__('يُجمع معلومات من مصادر حية ثم يُصاغ الخبر')),
                Textarea::make('instruction')
                    ->label(__('تعليمات أسلوب (اختياري)'))
                    ->placeholder(__('مثال: أسلوب تحليلي، ركّز على السعودية، تجنب العناوين الصادمة...'))
                    ->rows(2)
                    ->columnSpanFull(),
            ])
            ->action(function (array $data, Get $get, Set $set) use ($contentType): void {
                $ai = app(AiContentService::class);

                if (! $ai->isConfigured()) {
                    Notification::make()->danger()->title(__('الذكاء الاصطناعي غير مفعّل'))->body($ai->configurationMessage())->send();

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
                        ->title(__('تم توليد الخبر'))
                        ->body($searchNote ?: __('راجع المحتوى قبل النشر.'))
                        ->duration(8000)
                        ->send();
                } catch (\Throwable $e) {
                    Notification::make()->danger()->title(__('فشل توليد الخبر'))->body($e->getMessage())->duration(10000)->send();
                }
            });
    }

    public static function fillExcerptAction(string $contentType): Action
    {
        return Action::make('ai_excerpt')
            ->label(__('توليد المقتطف'))
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
            Notification::make()->danger()->title(__('الذكاء الاصطناعي غير مفعّل'))->body($ai->configurationMessage())->send();

            return;
        }

        try {
            $value = $ai->generateField($field, self::context($get, $contentType), $instruction);
            $set($field, $value);

            Notification::make()->success()->title(__('تم التوليد'))->send();
        } catch (\Throwable $e) {
            Notification::make()->danger()->title(__('فشل التوليد'))->body($e->getMessage())->send();
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
