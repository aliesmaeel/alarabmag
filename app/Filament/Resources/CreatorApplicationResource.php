<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\TranslatesResourceLabels;
use App\Filament\Resources\CreatorApplicationResource\Pages;
use App\Models\CreatorApplication;
use App\Support\CreatorApplicationCsv;
use App\Support\CreatorApplicationOptions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CreatorApplicationResource extends Resource
{
    use TranslatesResourceLabels;

    protected static ?string $model = CreatorApplication::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationLabel = 'طلبات صنّاع المحتوى';

    protected static ?string $modelLabel = 'طلب صانع محتوى';

    protected static ?string $pluralModelLabel = 'طلبات صنّاع المحتوى';

    protected static ?string $navigationGroup = 'الجمهور';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::query()->where('status', 'new')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    /** @return array<string, string> */
    protected static function statusOptions(): array
    {
        return array_map(fn (string $label): string => __($label), CreatorApplication::STATUSES);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('المتابعة'))
                ->description(__('حدّث حالة الطلب وأضف ملاحظات الفريق.'))
                ->schema([
                    Forms\Components\Select::make('status')
                        ->label(__('الحالة'))
                        ->options(static::statusOptions())
                        ->required()
                        ->native(false),
                    Forms\Components\Textarea::make('admin_notes')
                        ->label(__('ملاحظات داخلية'))
                        ->rows(4)
                        ->columnSpanFull(),
                ])
                ->columns(1),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make(__('بيانات مقدّم الطلب'))
                ->schema([
                    Infolists\Components\TextEntry::make('name')->label(__('الاسم الكامل')),
                    Infolists\Components\TextEntry::make('email')->label(__('البريد الإلكتروني'))->copyable(),
                    Infolists\Components\TextEntry::make('full_phone')->label(__('رقم التواصل'))->copyable(),
                    Infolists\Components\TextEntry::make('country')->label(__('الدولة')),
                    Infolists\Components\TextEntry::make('niche')->label(__('مجال المحتوى'))->badge(),
                    Infolists\Components\TextEntry::make('followers')
                        ->label(__('عدد المتابعين'))
                        ->placeholder('—'),
                    Infolists\Components\TextEntry::make('social_links')
                        ->label(__('روابط التواصل الاجتماعي'))
                        ->placeholder('—')
                        ->html()
                        ->formatStateUsing(fn (CreatorApplication $record): string => collect($record->social_link_list)
                            ->map(fn (string $link): string => sprintf(
                                '<a href="%s" target="_blank" rel="noopener noreferrer" class="fi-link text-primary-600 dark:text-primary-400 hover:underline">%s</a>',
                                e($link),
                                e($link),
                            ))
                            ->implode('<br>'))
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Infolists\Components\Section::make(__('نبذة عنه ومحتواه'))
                ->schema([
                    Infolists\Components\TextEntry::make('bio')
                        ->label('')
                        ->prose()
                        ->columnSpanFull(),
                ]),

            Infolists\Components\Section::make(__('المتابعة'))
                ->schema([
                    Infolists\Components\TextEntry::make('status')
                        ->label(__('الحالة'))
                        ->badge()
                        ->formatStateUsing(fn (?string $state): string => __(CreatorApplication::STATUSES[$state] ?? (string) $state))
                        ->color(fn (?string $state): string => match ($state) {
                            'approved' => 'success',
                            'rejected' => 'danger',
                            'reviewing' => 'info',
                            default => 'warning',
                        }),
                    Infolists\Components\TextEntry::make('created_at')
                        ->label(__('تاريخ الإرسال'))
                        ->dateTime('Y-m-d H:i'),
                    Infolists\Components\TextEntry::make('admin_notes')
                        ->label(__('ملاحظات داخلية'))
                        ->placeholder('—')
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('الاسم الكامل'))
                    ->searchable()
                    ->description(fn (CreatorApplication $record): string => $record->email)
                    ->wrap(),
                Tables\Columns\TextColumn::make('country')
                    ->label(__('الدولة'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('niche')
                    ->label(__('مجال المحتوى'))
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('followers')
                    ->label(__('المتابعون'))
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('full_phone')
                    ->label(__('رقم التواصل'))
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('social_links')
                    ->label(__('روابط التواصل الاجتماعي'))
                    ->badge()
                    ->limitList(2)
                    ->expandableLimitedList()
                    ->getStateUsing(fn (CreatorApplication $record): array => array_map(
                        fn (string $link): string => Str::limit(
                            (string) preg_replace('#^https?://(www\.)?#', '', $link),
                            28,
                        ),
                        $record->social_link_list,
                    )),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('الحالة'))
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => __(CreatorApplication::STATUSES[$state] ?? (string) $state))
                    ->color(fn (?string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'reviewing' => 'info',
                        default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('تاريخ الإرسال'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('الحالة'))
                    ->options(static::statusOptions()),
                Tables\Filters\SelectFilter::make('niche')
                    ->label(__('مجال المحتوى'))
                    ->options(fn (): array => array_combine(
                        CreatorApplicationOptions::niches(),
                        CreatorApplicationOptions::niches(),
                    ))
                    ->searchable(),
                Tables\Filters\SelectFilter::make('country')
                    ->label(__('الدولة'))
                    ->options(fn (): array => array_combine(
                        CreatorApplicationOptions::countries(),
                        CreatorApplicationOptions::countries(),
                    ))
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()->label(__('الحالة')),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('exportSelected')
                        ->label(__('تصدير المحدد CSV'))
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            $ids = $records->pluck('id');

                            return CreatorApplicationCsv::download(
                                CreatorApplication::query()->whereIn('id', $ids),
                                'creator-applications-selected-'.now()->format('Y-m-d-His'),
                            );
                        }),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label(__('تصدير CSV'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function ($livewire) {
                        return CreatorApplicationCsv::download(
                            $livewire->getFilteredTableQuery(),
                            'creator-applications-'.now()->format('Y-m-d-His'),
                        );
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCreatorApplications::route('/'),
            'view' => Pages\ViewCreatorApplication::route('/{record}'),
            'edit' => Pages\EditCreatorApplication::route('/{record}/edit'),
        ];
    }
}
