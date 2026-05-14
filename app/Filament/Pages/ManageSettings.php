<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Section;
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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('بيانات الموقع')->schema([
                    TextInput::make('site_name')->label('اسم الموقع'),
                    TextInput::make('site_tagline')->label('الشعار'),
                    Textarea::make('site_description')->label('الوصف')->rows(2)->columnSpanFull(),
                    TextInput::make('site_email')->label('البريد الإلكتروني')->email(),
                    TextInput::make('site_phone')->label('الهاتف'),
                ])->columns(2),

                Section::make('روابط التواصل')->schema([
                    TextInput::make('social_facebook')->label('فيسبوك')->url(),
                    TextInput::make('social_twitter')->label('تويتر / X')->url(),
                    TextInput::make('social_instagram')->label('انستغرام')->url(),
                    TextInput::make('social_youtube')->label('يوتيوب')->url(),
                    TextInput::make('social_linkedin')->label('لينكدإن')->url(),
                ])->columns(2),

                Section::make('SEO')->schema([
                    TextInput::make('seo_title')->label('عنوان SEO الافتراضي'),
                    Textarea::make('seo_description')->label('وصف SEO الافتراضي')->rows(2),
                    TextInput::make('seo_keywords')->label('كلمات مفتاحية'),
                ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        Setting::setMany($this->form->getState());

        Notification::make()
            ->title('تم حفظ الإعدادات بنجاح')
            ->success()
            ->send();
    }
}
