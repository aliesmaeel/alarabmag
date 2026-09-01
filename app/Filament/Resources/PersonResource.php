<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\TranslatesResourceLabels;
use App\Filament\Resources\PersonResource\Pages;
use App\Filament\Support\ImageUpload;
use App\Filament\Support\RichEditorField;
use App\Filament\Support\SeoFields;
use App\Models\Person;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class PersonResource extends Resource
{
    use TranslatesResourceLabels;

    protected static ?string $model = Person::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'الشخصيات';
    protected static ?string $modelLabel = 'شخصية';
    protected static ?string $pluralModelLabel = 'الشخصيات';
    protected static ?string $navigationGroup = 'المحتوى';
    protected static ?int $navigationSort = 3;
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('بيانات أساسية'))->schema([
                Forms\Components\TextInput::make('name')->label(__('الاسم'))->required()->maxLength(200),
                Forms\Components\TextInput::make('name_en')->label('Name (EN)')->maxLength(200),
                Forms\Components\TextInput::make('role')->label(__('الصفة / المنصب'))->maxLength(200),
                Forms\Components\Select::make('category')->label(__('الفئة'))->required()->native(false)
                    ->options([
                        'influencer' => __('مؤثر'),
                        'artist' => __('فنان'),
                        'doctor' => __('طبيب'),
                        'business' => __('رجل أعمال'),
                    ])
                    ->live(),
                Forms\Components\TextInput::make('country')->label(__('الدولة'))->maxLength(100),
                Forms\Components\TextInput::make('flag')->label(__('علم (إيموجي)'))->maxLength(10),
                ImageUpload::make('image_url', 'الصورة')->columnSpanFull(),
                Forms\Components\Toggle::make('featured')->label(__('مميز')),
            ])->columns(2),

            Forms\Components\Section::make(__('نبذة'))->schema([
                Forms\Components\Textarea::make('excerpt')->label(__('وصف مختصر'))->rows(3)->columnSpanFull(),
                RichEditorField::make('bio')->label(__('السيرة الكاملة'))->columnSpanFull(),
            ]),

            Forms\Components\Section::make(__('إحصائية بارزة'))->schema([
                Forms\Components\TextInput::make('stat')->label(__('الرقم / الإحصاء')),
                Forms\Components\TextInput::make('stat_label')->label(__('وصف الإحصاء')),
            ])->columns(2),

            Forms\Components\Section::make(__('بيانات المؤثر'))
                ->visible(fn (Forms\Get $get) => $get('category') === 'influencer')
                ->schema([
                    Forms\Components\TextInput::make('handle')->label(__('المعرّف (@)')),
                    Forms\Components\TextInput::make('platform')->label(__('المنصة')),
                    Forms\Components\TextInput::make('followers')->label(__('عدد المتابعين')),
                ])->columns(3),

            Forms\Components\Section::make(__('بيانات الطبيب'))
                ->visible(fn (Forms\Get $get) => $get('category') === 'doctor')
                ->schema([
                    Forms\Components\TextInput::make('hospital')->label(__('المستشفى')),
                    Forms\Components\TextInput::make('specialty')->label(__('التخصص')),
                    Forms\Components\TextInput::make('badge')->label(__('الوسام / اللقب')),
                ])->columns(3),

            Forms\Components\Section::make(__('بيانات رجل الأعمال'))
                ->visible(fn (Forms\Get $get) => $get('category') === 'business')
                ->schema([
                    Forms\Components\TextInput::make('company')->label(__('الشركة')),
                    Forms\Components\TextInput::make('net_worth')->label(__('الثروة')),
                ])->columns(2),

            SeoFields::section(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageUpload::column(),
                Tables\Columns\TextColumn::make('name')->label(__('الاسم'))->searchable(),
                Tables\Columns\TextColumn::make('role')->label(__('الصفة'))->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('category')->label(__('الفئة'))->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'influencer' => __('مؤثر'),
                        'artist' => __('فنان'),
                        'doctor' => __('طبيب'),
                        'business' => __('رجل أعمال'),
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'influencer' => 'info',
                        'artist' => 'warning',
                        'doctor' => 'success',
                        'business' => 'primary',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('country')->label(__('الدولة'))->toggleable(),
                Tables\Columns\IconColumn::make('featured')->label(__('مميز'))->boolean(),
                Tables\Columns\TextColumn::make('created_at')->label(__('التاريخ'))->dateTime('Y-m-d')->sortable()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('category')->label(__('الفئة'))
                    ->options([
                        'influencer' => __('مؤثر'),
                        'artist' => __('فنان'),
                        'doctor' => __('طبيب'),
                        'business' => __('رجل أعمال'),
                    ]),
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
            'index' => Pages\ListPeople::route('/'),
            'create' => Pages\CreatePerson::route('/create'),
            'edit' => Pages\EditPerson::route('/{record}/edit'),
        ];
    }
}
