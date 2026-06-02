<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DoctorResource\Pages;
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

class DoctorResource extends Resource
{
    protected static ?string $model = Person::class;

    protected static ?string $navigationIcon = 'heroicon-o-heart';
    protected static ?string $navigationLabel = 'الأطباء';
    protected static ?string $modelLabel = 'طبيب';
    protected static ?string $pluralModelLabel = 'الأطباء';
    protected static ?string $navigationGroup = 'المحتوى';
    protected static ?int $navigationSort = 4;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('category', 'doctor');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Hidden::make('category')->default('doctor'),

            Forms\Components\Section::make('بيانات أساسية')->schema([
                AiAssist::apply(
                    Forms\Components\TextInput::make('name')->label('الاسم')->required()->maxLength(200),
                    'name',
                    'doctor'
                ),
                Forms\Components\TextInput::make('name_en')->label('Name (EN)')->maxLength(200),
                Forms\Components\TextInput::make('role')->label('الدور / اللقب')->maxLength(200)
                    ->placeholder('مثال: استشاري جراحة الأعصاب'),
                Forms\Components\TextInput::make('country')->label('الدولة')->maxLength(100),
                Forms\Components\TextInput::make('flag')->label('علم (إيموجي)')->maxLength(10)->placeholder('🇸🇦'),
                Forms\Components\Toggle::make('featured')->label('مميز'),
                ImageUpload::make('image_url', 'الصورة')->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('البيانات الطبية')->schema([
                Forms\Components\TextInput::make('specialty')->label('التخصص الدقيق')->maxLength(200)
                    ->placeholder('جراحة الأعصاب، أورام...'),
                Forms\Components\TextInput::make('hospital')->label('المستشفى / المؤسسة')->maxLength(300)
                    ->placeholder('مايو كلينيك · أمريكا'),
                Forms\Components\TextInput::make('badge')->label('التكريم / اللقب الفخري')->maxLength(200)
                    ->placeholder('أفضل طبيب في العالم 2025'),
            ])->columns(2),

            Forms\Components\Section::make('نبذة')
                ->headerActions([
                    AiAssist::fillExcerptAction('doctor'),
                ])
                ->schema([
                AiAssist::apply(
                    Forms\Components\Textarea::make('excerpt')->label('وصف مختصر')->rows(3)
                        ->maxLength(1000)->columnSpanFull(),
                    'excerpt',
                    'doctor'
                ),
                Forms\Components\RichEditor::make('bio')->label('السيرة الكاملة')->columnSpanFull(),
            ]),

            Forms\Components\Section::make('إحصائية بارزة')->schema([
                Forms\Components\TextInput::make('stat')->label('الرقم / الإحصاء')
                    ->placeholder('مثال: 500+'),
                Forms\Components\TextInput::make('stat_label')->label('وصف الإحصاء')
                    ->placeholder('عملية ناجحة'),
            ])->columns(2)->collapsed(),

            SeoFields::section('doctor'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageUpload::column(),
                Tables\Columns\TextColumn::make('name')->label('الاسم')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('specialty')->label('التخصص')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('hospital')->label('المستشفى')->limit(40)->toggleable(),
                Tables\Columns\TextColumn::make('country')->label('الدولة')->toggleable(),
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
            'index'  => Pages\ListDoctors::route('/'),
            'create' => Pages\CreateDoctor::route('/create'),
            'edit'   => Pages\EditDoctor::route('/{record}/edit'),
        ];
    }
}
