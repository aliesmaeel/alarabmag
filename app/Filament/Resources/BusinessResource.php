<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BusinessResource\Pages;
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
use Illuminate\Database\Eloquent\Builder;

class BusinessResource extends Resource
{
    protected static ?string $model = Person::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationLabel = 'رجال الأعمال';
    protected static ?string $modelLabel = 'رجل أعمال';
    protected static ?string $pluralModelLabel = 'رجال الأعمال';
    protected static ?string $navigationGroup = 'المحتوى';
    protected static ?int $navigationSort = 5;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('category', 'business');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Hidden::make('category')->default('business'),

            Forms\Components\Section::make('بيانات أساسية')->schema([
                AiAssist::apply(
                    Forms\Components\TextInput::make('name')->label('الاسم')->required()->maxLength(200),
                    'name',
                    'business'
                ),
                Forms\Components\TextInput::make('name_en')->label('Name (EN)')->maxLength(200),
                Forms\Components\TextInput::make('role')->label('المنصب / اللقب')->maxLength(200)
                    ->placeholder('مثال: مؤسس ورئيس تنفيذي'),
                Forms\Components\TextInput::make('country')->label('الدولة')->maxLength(100),
                Forms\Components\TextInput::make('flag')->label('علم (إيموجي)')->maxLength(10)->placeholder('🇸🇦'),
                Forms\Components\Toggle::make('featured')->label('مميز'),
                ImageUpload::make('image_url', 'الصورة')->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('بيانات الأعمال')->schema([
                Forms\Components\TextInput::make('company')->label('الشركة / المجموعة')->maxLength(200),
                Forms\Components\TextInput::make('net_worth')->label('صافي الثروة / التقييم')->maxLength(100)
                    ->placeholder('مثال: $4.8B'),
                Forms\Components\TextInput::make('badge')->label('تكريم / لقب')->maxLength(200)
                    ->placeholder('رجل الأعمال العربي لعام 2026'),
            ])->columns(2),

            Forms\Components\Section::make('نبذة')
                ->headerActions([
                    AiAssist::fillExcerptAction('business'),
                ])
                ->schema([
                AiAssist::apply(
                    Forms\Components\Textarea::make('excerpt')->label('وصف مختصر')->rows(3)
                        ->maxLength(1000)->columnSpanFull(),
                    'excerpt',
                    'business'
                ),
                Forms\Components\RichEditor::make('bio')->label('السيرة الكاملة')->columnSpanFull(),
            ]),

            Forms\Components\Section::make('إحصائية بارزة')->schema([
                Forms\Components\TextInput::make('stat')->label('الرقم / الإحصاء')
                    ->placeholder('مثال: $4.8B'),
                Forms\Components\TextInput::make('stat_label')->label('وصف الإحصاء')
                    ->placeholder('صافي الثروة'),
            ])->columns(2)->collapsed(),

            SeoFields::section('business'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageUpload::column(),
                Tables\Columns\TextColumn::make('name')->label('الاسم')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('company')->label('الشركة')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('country')->label('الدولة')->toggleable(),
                Tables\Columns\TextColumn::make('net_worth')->label('الثروة / التقييم')->toggleable(),
                Tables\Columns\IconColumn::make('featured')->label('مميز')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->label('التاريخ')->dateTime('Y-m-d')->sortable()->toggleable(),
            ])
            ->filters([
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
            'index'  => Pages\ListBusinesses::route('/'),
            'create' => Pages\CreateBusiness::route('/create'),
            'edit'   => Pages\EditBusiness::route('/{record}/edit'),
        ];
    }
}
