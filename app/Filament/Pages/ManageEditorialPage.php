<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\TranslatesPageLabels;
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
    use TranslatesPageLabels;

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
                Section::make(__('رأس الصفحة'))->schema([
                    TextInput::make('editorial_title')
                        ->label(__('عنوان الصفحة'))
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Textarea::make('editorial_lead')
                        ->label(__('المقدمة تحت العنوان'))
                        ->rows(2)
                        ->columnSpanFull(),
                ]),

                Section::make(__('فريق التحرير'))->schema([
                    TextInput::make('editorial_team_title')
                        ->label(__('عنوان القسم'))
                        ->required()
                        ->maxLength(255),
                    Textarea::make('editorial_team_body')
                        ->label(__('نص القسم'))
                        ->rows(3)
                        ->columnSpanFull(),
                ]),

                Section::make(__('المحررة الأولى'))->schema([
                    TextInput::make('editorial_lead_editor_title')
                        ->label(__('عنوان القسم'))
                        ->required()
                        ->maxLength(255),
                    TextInput::make('editorial_lead_editor_name')
                        ->label(__('الاسم'))
                        ->required()
                        ->maxLength(255),
                    Textarea::make('editorial_lead_editor_bio')
                        ->label(__('الوصف'))
                        ->rows(2)
                        ->columnSpanFull(),
                ]),

                Section::make(__('فريق الأخبار'))->schema([
                    TextInput::make('editorial_news_title')
                        ->label(__('عنوان القسم'))
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Repeater::make('editorial_news_team')
                        ->label(__('أعضاء الفريق'))
                        ->schema([
                            TextInput::make('name')
                                ->label(__('الاسم'))
                                ->required()
                                ->maxLength(255),
                            TextInput::make('role')
                                ->label(__('المنصب / الوصف'))
                                ->required()
                                ->maxLength(500),
                        ])
                        ->columns(2)
                        ->defaultItems(1)
                        ->addActionLabel(__('إضافة عضو'))
                        ->columnSpanFull(),
                ]),

                Section::make(__('المدونات والآراء'))->schema([
                    TextInput::make('editorial_blogs_title')
                        ->label(__('عنوان القسم'))
                        ->required()
                        ->maxLength(255),
                    Textarea::make('editorial_blogs_body')
                        ->label(__('نص القسم'))
                        ->rows(3)
                        ->columnSpanFull(),
                ]),

                Section::make(__('التواصل التحريري'))->schema([
                    TextInput::make('editorial_contact_title')
                        ->label(__('عنوان القسم'))
                        ->required()
                        ->maxLength(255),
                    Textarea::make('editorial_contact_intro')
                        ->label(__('نص التواصل'))
                        ->rows(2)
                        ->columnSpanFull(),
                    TextInput::make('editorial_contact_email')
                        ->label(__('البريد الإلكتروني'))
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
            ->title(__('تم حفظ صفحة هيئة التحرير بنجاح'))
            ->success()
            ->send();
    }
}
