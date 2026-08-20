<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\TranslatesResourceLabels;
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
    use TranslatesResourceLabels;

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

            Forms\Components\Section::make(__('بيانات أساسية'))->schema([
                AiAssist::apply(
                    Forms\Components\TextInput::make('name')->label(__('الاسم'))->required()->maxLength(200),
                    'name',
                    'doctor'
                ),
                Forms\Components\TextInput::make('name_en')->label('Name (EN)')->maxLength(200),
                Forms\Components\TextInput::make('role')->label(__('الدور / اللقب'))->maxLength(200)
                    ->placeholder(__('مثال: استشاري جراحة الأعصاب')),
                Forms\Components\TextInput::make('country')->label(__('الدولة'))->maxLength(100),
                Forms\Components\TextInput::make('flag')->label(__('علم (إيموجي)'))->maxLength(10)->placeholder('🇸🇦'),
                Forms\Components\Toggle::make('featured')->label(__('مميز')),
                ImageUpload::make('image_url', 'الصورة')->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make(__('البيانات الطبية'))->schema([
                Forms\Components\TextInput::make('specialty')->label(__('التخصص الدقيق'))->maxLength(200)
                    ->placeholder(__('جراحة الأعصاب، أورام...')),
                Forms\Components\TextInput::make('hospital')->label(__('المستشفى / المؤسسة'))->maxLength(300)
                    ->placeholder(__('مايو كلينيك · أمريكا')),
                Forms\Components\TextInput::make('badge')->label(__('التكريم / اللقب الفخري'))->maxLength(200)
                    ->placeholder(__('أفضل طبيب في العالم 2025')),
            ])->columns(2),

            Forms\Components\Section::make(__('نبذة'))
                ->headerActions([
                    AiAssist::fillExcerptAction('doctor'),
                ])
                ->schema([
                AiAssist::apply(
                    Forms\Components\Textarea::make('excerpt')->label(__('وصف مختصر'))->rows(3)
                        ->maxLength(1000)->columnSpanFull(),
                    'excerpt',
                    'doctor'
                ),
                Forms\Components\RichEditor::make('bio')->label(__('السيرة الكاملة'))->columnSpanFull(),
            ]),

            Forms\Components\Section::make(__('إحصائية بارزة'))->schema([
                Forms\Components\TextInput::make('stat')->label(__('الرقم / الإحصاء'))
                    ->placeholder(__('مثال: 500+')),
                Forms\Components\TextInput::make('stat_label')->label(__('وصف الإحصاء'))
                    ->placeholder(__('عملية ناجحة')),
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
                Tables\Columns\TextColumn::make('name')->label(__('الاسم'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('specialty')->label(__('التخصص'))->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('hospital')->label(__('المستشفى'))->limit(40)->toggleable(),
                Tables\Columns\TextColumn::make('country')->label(__('الدولة'))->toggleable(),
                Tables\Columns\IconColumn::make('featured')->label(__('مميز'))->boolean(),
                Tables\Columns\TextColumn::make('created_at')->label(__('التاريخ'))->dateTime('Y-m-d')->sortable()->toggleable(),
            ])
            ->filters([
                TernaryFilter::make('featured')->label(__('مميز فقط')),
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
