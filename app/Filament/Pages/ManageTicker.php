<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\TranslatesPageLabels;
use App\Models\Setting;
use App\Services\SeoService;
use App\Support\SiteTicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageTicker extends Page implements HasForms
{
    use InteractsWithForms;
    use TranslatesPageLabels;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $navigationLabel = 'شريط العاجل';

    protected static ?string $title = 'شريط العاجل';

    protected static ?string $slug = 'ticker';

    protected static ?string $navigationGroup = 'المحتوى';

    protected static ?int $navigationSort = 0;

    protected static string $view = 'filament.pages.manage-ticker';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::getAllAsArray();

        $this->form->fill([
            'ticker_label' => $settings['ticker_label'] ?? 'عاجل',
            'ticker_speed' => (int) ($settings['ticker_speed'] ?? 5),
            'ticker_texts' => array_map(
                fn (string $text) => ['text' => $text],
                SiteTicker::texts(),
            ),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('شريط العاجل'))
                    ->description(__('نصوص مستقلة عن الأخبار تتحرك أعلى الموقع. أضف أكثر من نص ورتّبها بالسحب.'))
                    ->schema([
                        TextInput::make('ticker_label')
                            ->label(__('تسمية شريط العاجل'))
                            ->required()
                            ->maxLength(50)
                            ->default('عاجل')
                            ->helperText(__('النص الذهبي على يسار الشريط المتحرك في كل الصفحات')),
                        TextInput::make('ticker_speed')
                            ->label(__('سرعة الشريط'))
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(10)
                            ->step(1)
                            ->default(5)
                            ->helperText(__('من 1 (بطيء) إلى 10 (سريع)')),
                        Repeater::make('ticker_texts')
                            ->label(__('نصوص الشريط'))
                            ->schema([
                                TextInput::make('text')
                                    ->label(__('النص'))
                                    ->required()
                                    ->maxLength(500)
                                    ->placeholder(__('اكتب نصاً للشريط')),
                            ])
                            ->minItems(1)
                            ->defaultItems(1)
                            ->reorderable()
                            ->reorderableWithButtons()
                            ->itemLabel(fn (array $state): ?string => $state['text'] ?? null)
                            ->addActionLabel(__('إضافة نص'))
                            ->columnSpanFull(),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $texts = SiteTicker::normalize($state['ticker_texts'] ?? []);

        if ($texts === []) {
            Notification::make()
                ->title(__('أضف نصاً واحداً على الأقل'))
                ->danger()
                ->send();

            return;
        }

        Setting::setMany([
            'ticker_label' => $state['ticker_label'],
            'ticker_speed' => (string) max(1, min(10, (int) $state['ticker_speed'])),
            'ticker_texts' => json_encode($texts, JSON_UNESCAPED_UNICODE),
            'ticker_text' => $texts[0],
        ]);
        SeoService::forgetCache();

        Notification::make()
            ->title(__('تم حفظ الإعدادات بنجاح'))
            ->success()
            ->send();
    }
}
