<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\TranslatesResourceLabels;
use App\Filament\Resources\SongPollEntryResource\Pages;
use App\Filament\Support\AudioUpload;
use App\Filament\Support\ImageUpload;
use App\Models\SongPoll;
use App\Models\SongPollEntry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SongPollEntryResource extends Resource
{
    use TranslatesResourceLabels;

    protected static ?string $model = SongPollEntry::class;

    protected static ?string $navigationIcon = 'heroicon-o-musical-note';

    protected static ?string $navigationLabel = 'تصويت الأغاني';

    protected static ?string $modelLabel = 'أغنية';

    protected static ?string $pluralModelLabel = 'أغاني التصويت';

    protected static ?string $navigationGroup = 'المحتوى';

    protected static ?int $navigationSort = 8;

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    public static function songSchema(bool $includePoll = true): array
    {
        $fields = [];

        if ($includePoll) {
            $fields[] = Forms\Components\Select::make('song_poll_id')
                ->label(__('الاستفتاء'))
                ->relationship('poll', 'title')
                ->required()
                ->default(fn () => SongPoll::query()->latest('id')->value('id'))
                ->columnSpanFull();
        }

        $fields = [
            ...$fields,
            Forms\Components\TextInput::make('title')->label(__('اسم الأغنية'))->required()->maxLength(255),
            Forms\Components\TextInput::make('artist')->label(__('الفنان'))->required()->maxLength(255),
            Forms\Components\TextInput::make('country')->label(__('الدولة'))->maxLength(100),
            Forms\Components\TextInput::make('flag')->label(__('علم (إيموجي)'))->maxLength(16)->placeholder('🇪🇬'),
            AudioUpload::make('audio_path', 'ملف الأغنية (MP3)')
                ->helperText(__('ارفع ملف MP3 ليستمع الزوار على الموقع. عند الاستبدال أو الحذف يُزال الملف القديم.'))
                ->columnSpanFull(),
            Forms\Components\TextInput::make('listen_url')
                ->label(__('رابط استماع خارجي (اختياري)'))
                ->url()
                ->maxLength(1000)
                ->helperText(__('يُستخدم إن لم يُرفع ملف MP3.'))
                ->columnSpanFull(),
            Forms\Components\Textarea::make('excerpt')->label(__('نبذة'))->rows(2)->maxLength(500)->columnSpanFull(),
            ImageUpload::make('image_url', 'الغلاف')->columnSpanFull(),
            Forms\Components\TextInput::make('sort_order')->label(__('الترتيب'))->numeric()->default(0),
            Forms\Components\TextInput::make('votes_count')->label(__('عدد الأصوات'))->numeric()->default(0)->disabledOn('create'),
        ];

        return $fields;
    }

    /**
     * @return array<int, \Filament\Tables\Columns\Column>
     */
    public static function songTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('sort_order')->label('#')->sortable(),
            ImageUpload::column('image_url', 'الغلاف'),
            Tables\Columns\TextColumn::make('title')->label(__('الأغنية'))->searchable()->sortable(),
            Tables\Columns\TextColumn::make('artist')->label(__('الفنان'))->searchable(),
            Tables\Columns\IconColumn::make('audio_path')
                ->label(__('MP3'))
                ->boolean()
                ->getStateUsing(fn (SongPollEntry $record): bool => filled($record->audio_path)),
            Tables\Columns\TextColumn::make('country')->label(__('الدولة')),
            Tables\Columns\TextColumn::make('votes_count')->label(__('الأصوات'))->sortable()->numeric(),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('الأغنية'))
                ->schema(static::songSchema())
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('votes_count', 'desc')
            ->columns([
                ...static::songTableColumns(),
                Tables\Columns\TextColumn::make('poll.title')->label(__('الاستفتاء'))->toggleable(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSongPollEntries::route('/'),
            'create' => Pages\CreateSongPollEntry::route('/create'),
            'edit' => Pages\EditSongPollEntry::route('/{record}/edit'),
        ];
    }
}
