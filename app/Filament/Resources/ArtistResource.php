<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArtistResource\Pages;
use App\Filament\Support\AiAssist;
use App\Filament\Support\ImageUpload;
use App\Filament\Support\SeoFields;
use App\Models\Person;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class ArtistResource extends Resource
{
    protected static ?string $model = Person::class;

    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';
    protected static ?string $navigationLabel = 'الفنانون';
    protected static ?string $modelLabel = 'فنان';
    protected static ?string $pluralModelLabel = 'الفنانون';
    protected static ?string $navigationGroup = 'المحتوى';
    protected static ?int $navigationSort = 6;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('category', 'artist');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Hidden::make('category')->default('artist'),

            Forms\Components\Section::make('بيانات أساسية')->schema([
                AiAssist::apply(
                    Forms\Components\TextInput::make('name')->label('الاسم')->required()->maxLength(200),
                    'name',
                    'artist'
                ),
                Forms\Components\TextInput::make('name_en')->label('Name (EN)')->maxLength(200),
                Forms\Components\TextInput::make('role')->label('التخصص الفني')->maxLength(200)
                    ->placeholder('مخرج سينمائي، فنان تشكيلي، منتج موسيقي...'),
                Forms\Components\TextInput::make('country')->label('الدولة')->maxLength(100),
                Forms\Components\TextInput::make('flag')->label('علم (إيموجي)')->maxLength(10)->placeholder('🇱🇧'),
                Forms\Components\Toggle::make('featured')->label('مميز'),
                ImageUpload::make('image_url', 'الصورة')->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('نبذة')
                ->headerActions([
                    AiAssist::fillExcerptAction('artist'),
                ])
                ->schema([
                AiAssist::apply(
                    Forms\Components\Textarea::make('excerpt')->label('وصف مختصر')->rows(3)
                        ->maxLength(1000)->columnSpanFull()
                        ->helperText('سطر أو سطران يُلخّصان أبرز ما يميّز الفنان — يظهر تحت اسمه.'),
                    'excerpt',
                    'artist'
                ),
                Forms\Components\RichEditor::make('bio')->label('السيرة الكاملة')->columnSpanFull(),
            ]),

            Forms\Components\Section::make('إنجاز / إحصائية')->schema([
                Forms\Components\TextInput::make('stat')->label('الإنجاز')
                    ->placeholder('مثال: جائزة كان 2026'),
                Forms\Components\TextInput::make('stat_label')->label('وصف الإنجاز')
                    ->placeholder('أفضل مخرجة'),
            ])->columns(2)->collapsed(),

            SeoFields::section('artist'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageUpload::column(),
                Tables\Columns\TextColumn::make('name')->label('الاسم')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('role')->label('التخصص الفني')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('country')->label('الدولة')->toggleable(),
                Tables\Columns\IconColumn::make('featured')->label('مميز')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->label('التاريخ')->dateTime('Y-m-d')->sortable()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('role')->label('التخصص')->options(fn () =>
                    Person::query()->where('category', 'artist')
                        ->whereNotNull('role')->distinct()->pluck('role', 'role')->toArray()
                ),
                TernaryFilter::make('featured')->label('مميز فقط'),
            ])
            ->actions([
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
            'index'  => Pages\ListArtists::route('/'),
            'create' => Pages\CreateArtist::route('/create'),
            'edit'   => Pages\EditArtist::route('/{record}/edit'),
        ];
    }
}
