<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonResource\Pages;
use App\Filament\Support\ImageUpload;
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
    protected static ?string $model = Person::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'الشخصيات';
    protected static ?string $modelLabel = 'شخصية';
    protected static ?string $pluralModelLabel = 'الشخصيات';
    protected static ?string $navigationGroup = 'المحتوى';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('بيانات أساسية')->schema([
                Forms\Components\TextInput::make('name')->label('الاسم')->required()->maxLength(200),
                Forms\Components\TextInput::make('name_en')->label('Name (EN)')->maxLength(200),
                Forms\Components\TextInput::make('role')->label('الصفة / المنصب')->maxLength(200),
                Forms\Components\Select::make('category')->label('الفئة')->required()->native(false)
                    ->options([
                        'influencer' => 'مؤثر',
                        'artist' => 'فنان',
                        'doctor' => 'طبيب',
                        'business' => 'رجل أعمال',
                    ])
                    ->live(),
                Forms\Components\TextInput::make('country')->label('الدولة')->maxLength(100),
                Forms\Components\TextInput::make('flag')->label('علم (إيموجي)')->maxLength(10),
                ImageUpload::make('image_url', 'الصورة')->columnSpanFull(),
                Forms\Components\Toggle::make('featured')->label('مميز'),
            ])->columns(2),

            Forms\Components\Section::make('نبذة')->schema([
                Forms\Components\Textarea::make('excerpt')->label('وصف مختصر')->rows(3)->columnSpanFull(),
                Forms\Components\RichEditor::make('bio')->label('السيرة الكاملة')->columnSpanFull(),
            ]),

            Forms\Components\Section::make('إحصائية بارزة')->schema([
                Forms\Components\TextInput::make('stat')->label('الرقم / الإحصاء'),
                Forms\Components\TextInput::make('stat_label')->label('وصف الإحصاء'),
            ])->columns(2),

            Forms\Components\Section::make('بيانات المؤثر')
                ->visible(fn (Forms\Get $get) => $get('category') === 'influencer')
                ->schema([
                    Forms\Components\TextInput::make('handle')->label('المعرّف (@)'),
                    Forms\Components\TextInput::make('platform')->label('المنصة'),
                    Forms\Components\TextInput::make('followers')->label('عدد المتابعين'),
                ])->columns(3),

            Forms\Components\Section::make('بيانات الطبيب')
                ->visible(fn (Forms\Get $get) => $get('category') === 'doctor')
                ->schema([
                    Forms\Components\TextInput::make('hospital')->label('المستشفى'),
                    Forms\Components\TextInput::make('specialty')->label('التخصص'),
                    Forms\Components\TextInput::make('badge')->label('الوسام / اللقب'),
                ])->columns(3),

            Forms\Components\Section::make('بيانات رجل الأعمال')
                ->visible(fn (Forms\Get $get) => $get('category') === 'business')
                ->schema([
                    Forms\Components\TextInput::make('company')->label('الشركة'),
                    Forms\Components\TextInput::make('net_worth')->label('الثروة'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageUpload::column(),
                Tables\Columns\TextColumn::make('name')->label('الاسم')->searchable(),
                Tables\Columns\TextColumn::make('role')->label('الصفة')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('category')->label('الفئة')->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'influencer' => 'مؤثر',
                        'artist' => 'فنان',
                        'doctor' => 'طبيب',
                        'business' => 'رجل أعمال',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'influencer' => 'info',
                        'artist' => 'warning',
                        'doctor' => 'success',
                        'business' => 'primary',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('country')->label('الدولة')->toggleable(),
                Tables\Columns\IconColumn::make('featured')->label('مميز')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->label('التاريخ')->dateTime('Y-m-d')->sortable()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('category')->label('الفئة')
                    ->options([
                        'influencer' => 'مؤثر',
                        'artist' => 'فنان',
                        'doctor' => 'طبيب',
                        'business' => 'رجل أعمال',
                    ]),
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
            'index' => Pages\ListPeople::route('/'),
            'create' => Pages\CreatePerson::route('/create'),
            'edit' => Pages\EditPerson::route('/{record}/edit'),
        ];
    }
}
