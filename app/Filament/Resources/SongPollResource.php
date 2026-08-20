<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\TranslatesResourceLabels;
use App\Filament\Resources\SongPollResource\Pages;
use App\Filament\Resources\SongPollResource\RelationManagers;
use App\Models\SongPoll;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SongPollResource extends Resource
{
    use TranslatesResourceLabels;

    protected static ?string $model = SongPoll::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'الاستفتاءات';

    protected static ?string $modelLabel = 'استفتاء';

    protected static ?string $pluralModelLabel = 'الاستفتاءات';

    protected static ?string $navigationGroup = 'المحتوى';

    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('بيانات الاستفتاء'))->schema([
                Forms\Components\TextInput::make('title')
                    ->label(__('عنوان الاستفتاء'))
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (?string $state, Set $set, ?string $operation): void {
                        if ($operation === 'create' && filled($state)) {
                            $set('slug', SongPoll::uniqueSlug($state));
                        }
                    })
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('slug')
                    ->label(__('الرابط (slug)'))
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->placeholder(__('يُنشأ تلقائياً من العنوان'))
                    ->suffixAction(
                        Forms\Components\Actions\Action::make('generate_slug')
                            ->icon('heroicon-m-arrow-path')
                            ->tooltip(__('توليد الرابط من العنوان'))
                            ->action(function (Forms\Get $get, Set $set, ?SongPoll $record): void {
                                $title = trim((string) $get('title'));

                                if (blank($title)) {
                                    \Filament\Notifications\Notification::make()
                                        ->warning()->title(__('أدخل العنوان أولاً'))->send();

                                    return;
                                }

                                $set('slug', SongPoll::uniqueSlug($title, $record?->id));
                            })
                    )
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('title_en')
                    ->label(__('العنوان بالإنجليزية'))
                    ->maxLength(255),
                Forms\Components\TextInput::make('eyebrow')
                    ->label(__('السطر العلوي'))
                    ->maxLength(255)
                    ->placeholder('Vote · أغنية العام'),
                Forms\Components\Textarea::make('subtitle')
                    ->label(__('الوصف تحت العنوان'))
                    ->rows(2)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('year')
                    ->label(__('سنة الإصدارات'))
                    ->numeric()
                    ->minValue(2000)
                    ->maxValue(2100)
                    ->default((int) now()->year - 1),
            ])->columns(2),

            Forms\Components\Section::make(__('الإعدادات'))->schema([
                Forms\Components\Select::make('status')
                    ->label(__('الحالة'))
                    ->options([
                        'published' => __('منشور'),
                        'draft' => __('مسودة'),
                    ])
                    ->required()
                    ->default('published')
                    ->native(false),
                Forms\Components\DateTimePicker::make('starts_at')
                    ->label(__('تاريخ البداية'))
                    ->native(false)
                    ->seconds(false),
                Forms\Components\DateTimePicker::make('ends_at')
                    ->label(__('تاريخ النهاية'))
                    ->native(false)
                    ->seconds(false),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('العنوان'))
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('year')
                    ->label(__('السنة'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('الحالة'))
                    ->badge()
                    ->color(fn (string $state): string => $state === 'published' ? 'success' : 'gray')
                    ->formatStateUsing(fn (string $state) => $state === 'published' ? __('منشور') : __('مسودة')),
                Tables\Columns\TextColumn::make('entries_count')
                    ->counts('entries')
                    ->label(__('الأغاني'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('votes_count')
                    ->counts('votes')
                    ->label(__('الأصوات'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('starts_at')
                    ->label(__('تاريخ البداية'))
                    ->dateTime('Y-m-d H:i')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('ends_at')
                    ->label(__('تاريخ النهاية'))
                    ->dateTime('Y-m-d H:i')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('الحالة'))
                    ->options([
                        'published' => __('منشور'),
                        'draft' => __('مسودة'),
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('preview')
                    ->label(__('معاينة الصفحة'))
                    ->icon('heroicon-o-eye')
                    ->url(fn (): string => route('vote.index'))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\EntriesRelationManager::class,
            RelationManagers\VotesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSongPolls::route('/'),
            'create' => Pages\CreateSongPoll::route('/create'),
            'edit' => Pages\EditSongPoll::route('/{record}/edit'),
        ];
    }
}
