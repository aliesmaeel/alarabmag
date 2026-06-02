<?php

namespace App\Filament\Pages;

use App\Filament\Support\KeywordsInput;
use App\Models\Setting;
use App\Services\SeoService;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Tabs;
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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('settings')->tabs([
                    Tabs\Tab::make('الموقع')->schema([
                        Section::make('بيانات الموقع')->schema([
                            TextInput::make('site_name')->label('اسم الموقع'),
                            TextInput::make('site_tagline')->label('الشعار'),
                            Textarea::make('site_description')->label('الوصف')->rows(2)->columnSpanFull(),
                            TextInput::make('site_email')->label('البريد الإلكتروني')->email(),
                            TextInput::make('site_phone')->label('الهاتف'),
                            TextInput::make('ticker_label')
                                ->label('تسمية شريط العاجل')
                                ->default('عاجل')
                                ->maxLength(50)
                                ->helperText('النص الذهبي على يسار الشريط المتحرك في كل الصفحات'),
                        ])->columns(2),

                        Section::make('روابط التواصل')->schema([
                            TextInput::make('social_facebook')->label('فيسبوك')->url(),
                            TextInput::make('social_twitter')->label('تويتر / X')->url(),
                            TextInput::make('social_instagram')->label('انستغرام')->url(),
                            TextInput::make('social_youtube')->label('يوتيوب')->url(),
                            TextInput::make('social_linkedin')->label('لينكدإن')->url(),
                        ])->columns(2),
                    ]),

                    Tabs\Tab::make('SEO عام')->schema([
                        Section::make('الإعدادات الافتراضية')->description('تُستخدم عندما لا يُحدد SEO خاص بصفحة أو خبر.')
                            ->schema([
                                TextInput::make('seo_title')->label('عنوان SEO الافتراضي')->maxLength(255)->columnSpanFull(),
                                Textarea::make('seo_description')->label('وصف SEO الافتراضي (meta description)')->rows(3)->columnSpanFull(),
                                KeywordsInput::make('seo_keywords', 'كلمات مفتاحية افتراضية')->columnSpanFull(),
                                TextInput::make('og_site_name')->label('og:site_name')->maxLength(255),
                                TextInput::make('og_default_image')
                                    ->label('صورة OG الافتراضية (رابط)')
                                    ->url()
                                    ->helperText('تُستخدم عند عدم وجود صورة للخبر/الصفحة. يمكنك استخدام /logo.png')
                                    ->columnSpanFull(),
                                TextInput::make('twitter_card')
                                    ->label('نوع بطاقة تويتر')
                                    ->default('summary_large_image')
                                    ->helperText('مثال: summary_large_image أو summary'),
                            ])->columns(2),
                    ]),

                    Tabs\Tab::make('SEO الصفحات')->schema([
                        ...collect(['home' => 'الرئيسية', 'news' => 'الأخبار', 'blogs' => 'المدونات', 'doctors' => 'الأطباء', 'influencers' => 'المؤثرون', 'artists' => 'الفنانون', 'business' => 'الأعمال', 'fashion' => 'الموضة'])
                            ->map(fn (string $label, string $key) => Section::make($label)
                                ->schema([
                                    TextInput::make("seo_{$key}_title")->label('عنوان الصفحة (title)')->maxLength(255)->columnSpanFull(),
                                    Textarea::make("seo_{$key}_description")->label('وصف الصفحة (meta / og:description)')->rows(2)->columnSpanFull(),
                                    KeywordsInput::make("seo_{$key}_keywords", 'كلمات مفتاحية')->columnSpanFull(),
                                    TextInput::make("og_{$key}_title")->label('og:title (اختياري)')->maxLength(255)->columnSpanFull(),
                                    TextInput::make("og_{$key}_image")->label('og:image (رابط، اختياري)')->url()->columnSpanFull(),
                                ])
                                ->collapsed()
                                ->columns(1))
                            ->values()
                            ->all(),
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
