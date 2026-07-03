<?php

namespace App\Filament\Pages;

use App\Filament\Support\KeywordsInput;
use App\Filament\Support\SeoPageSettings;
use App\Models\Setting;
use App\Services\AiContentService;
use App\Services\SeoService;
use App\Support\SiteBrand;
use App\Support\SiteSeoDefaults;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'الإعدادات';

    protected static ?string $title = 'إعدادات الموقع';

    protected static ?int $navigationSort = 99;

    protected static string $view = 'filament.pages.manage-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(Setting::getAllAsArray());
    }

    protected function getHeaderActions(): array
    {
        $ai = app(AiContentService::class);

        return [
            Action::make('seedSeo')
                ->label('تعبئة SEO تلقائياً')
                ->icon('heroicon-o-document-duplicate')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('تعبئة SEO الافتراضي')
                ->modalDescription('يملأ SEO العام وكل الصفحات بقيم جاهزة لمجلة العرب. لن يمسّ بيانات الموقع الأخرى.')
                ->modalSubmitActionLabel('تعبئة وحفظ')
                ->action(function (): void {
                    $this->applySeoSettings(SiteSeoDefaults::all(), 'تم تعبئة SEO الافتراضي لكل الصفحات');
                }),

            Action::make('aiSeo')
                ->label('توليد SEO بالذكاء الاصطناعي')
                ->icon('heroicon-o-sparkles')
                ->color('warning')
                ->disabled(! $ai->isConfigured())
                ->tooltip($ai->isConfigured() ? 'يولّد عناوين وأوصاف لكل الصفحات دفعة واحدة' : $ai->configurationMessage())
                ->modalHeading('توليد SEO بالذكاء الاصطناعي')
                ->modalDescription('يستخدم مزوّد AI المُعدّ في .env لتوليد SEO لكل صفحات الموقع.')
                ->modalSubmitActionLabel('توليد وحفظ')
                ->form([
                    Textarea::make('instruction')
                        ->label('تعليمات إضافية (اختياري)')
                        ->placeholder('مثال: ركّز على دبي والخليج، أسلوب رسمي...')
                        ->rows(3),
                ])
                ->action(function (array $data) use ($ai): void {
                    if (! $ai->isConfigured()) {
                        Notification::make()->danger()->title('الذكاء الاصطناعي غير مفعّل')->body($ai->configurationMessage())->send();

                        return;
                    }

                    try {
                        $settings = $ai->generateSitePagesSeo($data['instruction'] ?? null);
                        $this->applySeoSettings($settings, 'تم توليد SEO بالذكاء الاصطناعي');
                    } catch (\Throwable $e) {
                        Notification::make()->danger()->title('فشل التوليد')->body($e->getMessage())->send();
                    }
                }),
        ];
    }

    /**
     * @param  array<string, string>  $seoSettings
     */
    protected function applySeoSettings(array $seoSettings, string $successTitle): void
    {
        Setting::setMany($seoSettings);
        SeoService::forgetCache();

        $this->form->fill(array_merge($this->data ?? [], $seoSettings));

        Notification::make()->success()->title($successTitle)->send();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('settings')->tabs([
                    Tabs\Tab::make('الموقع')->schema([
                        Section::make('بيانات الموقع')->schema([
                            TextInput::make('site_name')->label('اسم الموقع (عربي)')->default(SiteBrand::NAME_AR),
                            TextInput::make('site_name_en')->label('اسم الموقع (إنجليزي)')->default(SiteBrand::NAME_EN),
                            TextInput::make('site_tagline')->label('الشعار'),
                            Textarea::make('site_description')->label('وصف الموقع')->rows(2)->columnSpanFull(),
                            TextInput::make('editor_email')->label('بريد التحرير')->email(),
                            TextInput::make('site_email')->label('البريد الإلكتروني العام')->email(),
                            TextInput::make('site_phone')->label('الهاتف'),
                            TextInput::make('ticker_label')
                                ->label('تسمية شريط العاجل')
                                ->default('عاجل')
                                ->maxLength(50)
                                ->helperText('النص الذهبي على يسار الشريط المتحرك في كل الصفحات'),
                        ])->columns(2),

                        Section::make('روابط التواصل')->schema([
                            TextInput::make('facebook')->label('فيسبوك'),
                            TextInput::make('twitter')->label('تويتر / X'),
                            TextInput::make('instagram')->label('انستغرام'),
                            TextInput::make('youtube')->label('يوتيوب'),
                            TextInput::make('tiktok')->label('تيك توك'),
                            TextInput::make('whatsapp')->label('واتساب'),
                        ])->columns(2),
                    ]),

                    Tabs\Tab::make('SEO عام')
                        ->icon('heroicon-o-magnifying-glass')
                        ->schema([
                            Section::make('الإعدادات الافتراضية')
                                ->description('تُستخدم عندما لا يُحدد SEO خاص بصفحة أو مقال.')
                                ->schema([
                                    TextInput::make('seo_title')
                                        ->label('عنوان SEO الافتراضي')
                                        ->placeholder(SiteBrand::defaultTitle())
                                        ->maxLength(255)
                                        ->columnSpanFull(),
                                    Textarea::make('seo_description')
                                        ->label('وصف SEO الافتراضي (meta description)')
                                        ->placeholder(SiteBrand::defaultDescription())
                                        ->rows(3)
                                        ->columnSpanFull(),
                                    KeywordsInput::make('seo_keywords', 'كلمات مفتاحية افتراضية')
                                        ->columnSpanFull(),
                                    TextInput::make('og_site_name')
                                        ->label('og:site_name')
                                        ->default(SiteBrand::NAME_AR)
                                        ->maxLength(255),
                                    TextInput::make('og_default_image')
                                        ->label('صورة OG الافتراضية (رابط)')
                                        ->helperText('تُستخدم عند عدم وجود صورة للمقال/الصفحة. مثال: /logo.png')
                                        ->columnSpanFull(),
                                    TextInput::make('twitter_card')
                                        ->label('نوع بطاقة تويتر')
                                        ->default('summary_large_image')
                                        ->helperText('مثال: summary_large_image أو summary'),
                                    TextInput::make('google_site_verification')
                                        ->label('Google Search Console (تحقق الموقع)')
                                        ->helperText('القيمة فقط من وسم meta google-site-verification (بدون علامات HTML)')
                                        ->columnSpanFull(),
                                ])->columns(2),
                        ]),

                    Tabs\Tab::make('SEO الصفحات')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Section::make('تعبئة سريعة')
                                ->description('لا حاجة لملء الحقول يدوياً — استخدم الأزرار أعلى الصفحة: «تعبئة SEO تلقائياً» للقيم الجاهزة، أو «توليد SEO بالذكاء الاصطناعي» إذا كان GROQ_API_KEY مفعّلاً. يمكنك أيضاً تشغيل: php artisan seo:seed')
                                ->collapsed(),

                            Section::make('الصفحات الرئيسية')
                                ->description('عناوين وأوصاف صفحات الأقسام والرئيسية كما تظهر في Google.')
                                ->schema(SeoPageSettings::mainPages())
                                ->collapsible(),

                            Section::make('الصفحات الثابتة')
                                ->description('عن المجلة، اتصل بنا، الخصوصية، وغيرها.')
                                ->schema(SeoPageSettings::staticPages())
                                ->collapsed(),
                        ]),
                ])->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        Setting::setMany($this->form->getState());
        SeoService::forgetCache();

        Notification::make()
            ->title('تم حفظ الإعدادات بنجاح')
            ->success()
            ->send();
    }
}
