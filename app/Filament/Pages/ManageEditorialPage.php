<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Services\SeoService;
use App\Support\EditorialPage;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageEditorialPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'هيئة التحرير';

    protected static ?string $title = 'صفحة هيئة التحرير';

    protected static ?string $navigationGroup = 'الموقع';

    protected static ?int $navigationSort = 5;

    protected static string $view = 'filament.pages.manage-editorial-page';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(EditorialPage::formData());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('رأس الصفحة')->schema([
                    TextInput::make('editorial_title')
                        ->label('عنوان الصفحة')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Textarea::make('editorial_lead')
                        ->label('المقدمة تحت العنوان')
                        ->rows(2)
                        ->columnSpanFull(),
                ]),

                Section::make('فريق التحرير')->schema([
                    TextInput::make('editorial_team_title')
                        ->label('عنوان القسم')
                        ->required()
                        ->maxLength(255),
                    Textarea::make('editorial_team_body')
                        ->label('نص القسم')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),

                Section::make('المحررة الأولى')->schema([
                    TextInput::make('editorial_lead_editor_title')
                        ->label('عنوان القسم')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('editorial_lead_editor_name')
                        ->label('الاسم')
                        ->required()
                        ->maxLength(255),
                    Textarea::make('editorial_lead_editor_bio')
                        ->label('الوصف')
                        ->rows(2)
                        ->columnSpanFull(),
                ]),

                Section::make('فريق الأخبار')->schema([
                    TextInput::make('editorial_news_title')
                        ->label('عنوان القسم')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Repeater::make('editorial_news_team')
                        ->label('أعضاء الفريق')
                        ->schema([
                            TextInput::make('name')
                                ->label('الاسم')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('role')
                                ->label('المنصب / الوصف')
                                ->required()
                                ->maxLength(500),
                        ])
                        ->columns(2)
                        ->defaultItems(1)
                        ->addActionLabel('إضافة عضو')
                        ->columnSpanFull(),
                ]),

                Section::make('المدونات والآراء')->schema([
                    TextInput::make('editorial_blogs_title')
                        ->label('عنوان القسم')
                        ->required()
                        ->maxLength(255),
                    Textarea::make('editorial_blogs_body')
                        ->label('نص القسم')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),

                Section::make('التواصل التحريري')->schema([
                    TextInput::make('editorial_contact_title')
                        ->label('عنوان القسم')
                        ->required()
                        ->maxLength(255),
                    Textarea::make('editorial_contact_intro')
                        ->label('نص التواصل')
                        ->rows(2)
                        ->columnSpanFull(),
                    TextInput::make('editorial_contact_email')
                        ->label('البريد الإلكتروني')
                        ->email()
                        ->required()
                        ->maxLength(255),
                ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $state['editorial_news_team'] = json_encode(
            $state['editorial_news_team'] ?? [],
            JSON_UNESCAPED_UNICODE,
        );

        Setting::setMany($state);
        SeoService::forgetCache();

        Notification::make()
            ->title('تم حفظ صفحة هيئة التحرير بنجاح')
            ->success()
            ->send();
    }
}
