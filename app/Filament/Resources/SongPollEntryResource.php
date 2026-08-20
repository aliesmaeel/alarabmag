<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SongPollEntryResource\Pages;
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
    protected static ?string $model = SongPollEntry::class;

    protected static ?string $navigationIcon = 'heroicon-o-musical-note';

    protected static ?string $navigationLabel = 'تصويت الأغاني';

    protected static ?string $modelLabel = 'أغنية';

    protected static ?string $pluralModelLabel = 'أغاني التصويت';

    protected static ?string $navigationGroup = 'المحتوى';

    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('الأغنية')->schema([
                Forms\Components\Select::make('song_poll_id')
                    ->label('الاستفتاء')
                    ->relationship('poll', 'title')
                    ->required()
                    ->default(fn () => SongPoll::query()->latest('id')->value('id'))
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('title')->label('اسم الأغنية')->required()->maxLength(255),
                Forms\Components\TextInput::make('artist')->label('الفنان')->required()->maxLength(255),
                Forms\Components\TextInput::make('country')->label('الدولة')->maxLength(100),
                Forms\Components\TextInput::make('flag')->label('علم (إيموجي)')->maxLength(16)->placeholder('🇪🇬'),
                Forms\Components\TextInput::make('listen_url')->label('رابط الاستماع')->url()->maxLength(1000)->columnSpanFull(),
                Forms\Components\Textarea::make('excerpt')->label('نبذة')->rows(2)->maxLength(500)->columnSpanFull(),
                ImageUpload::make('image_url', 'الغلاف')->columnSpanFull(),
                Forms\Components\TextInput::make('sort_order')->label('الترتيب')->numeric()->default(0),
                Forms\Components\TextInput::make('votes_count')->label('عدد الأصوات')->numeric()->default(0)->disabledOn('create'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('votes_count', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')->label('#')->sortable(),
                ImageUpload::column('image_url', 'الغلاف'),
                Tables\Columns\TextColumn::make('title')->label('الأغنية')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('artist')->label('الفنان')->searchable(),
                Tables\Columns\TextColumn::make('country')->label('الدولة'),
                Tables\Columns\TextColumn::make('votes_count')->label('الأصوات')->sortable()->numeric(),
                Tables\Columns\TextColumn::make('poll.title')->label('الاستفتاء')->toggleable(),
            ])
            ->actions([
                Tables\Actions\Action::make('preview')
                    ->label('معاينة الصفحة')
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
