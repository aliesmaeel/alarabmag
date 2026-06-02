<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InfluencerResource\Pages;
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

class InfluencerResource extends Resource
{
    protected static ?string $model = Person::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationLabel = 'المؤثرون';
    protected static ?string $modelLabel = 'مؤثر';
    protected static ?string $pluralModelLabel = 'المؤثرون';
    protected static ?string $navigationGroup = 'المحتوى';
    protected static ?int $navigationSort = 5;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('category', 'influencer');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Hidden::make('category')->default('influencer'),

            Forms\Components\Section::make('بيانات أساسية')->schema([
                AiAssist::apply(
                    Forms\Components\TextInput::make('name')->label('الاسم')->required()->maxLength(200),
                    'name',
                    'influencer'
                ),
                Forms\Components\TextInput::make('name_en')->label('Name (EN)')->maxLength(200),
                Forms\Components\TextInput::make('role')->label('المجال / التخصص')->maxLength(200)
                    ->placeholder('موضة ونمط حياة، تقنية، طبخ...'),
                Forms\Components\TextInput::make('country')->label('الدولة')->maxLength(100),
                Forms\Components\TextInput::make('flag')->label('علم (إيموجي)')->maxLength(10)->placeholder('🇦🇪'),
                Forms\Components\Toggle::make('featured')->label('مميز'),
                ImageUpload::make('image_url', 'الصورة')->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('بيانات السوشيال ميديا')->schema([
                Forms\Components\TextInput::make('handle')->label('المعرّف (@)')->maxLength(100)
                    ->placeholder('@username'),
                Forms\Components\TextInput::make('platform')->label('المنصة الرئيسية')->maxLength(100)
                    ->placeholder('Instagram، TikTok، YouTube...'),
                Forms\Components\TextInput::make('followers')->label('عدد المتابعين')->maxLength(50)
                    ->placeholder('12.4M'),
            ])->columns(3),

            Forms\Components\Section::make('نبذة')
                ->headerActions([
                    AiAssist::fillExcerptAction('influencer'),
                ])
                ->schema([
                AiAssist::apply(
                    Forms\Components\Textarea::make('excerpt')->label('وصف مختصر')->rows(3)
                        ->maxLength(1000)->columnSpanFull(),
                    'excerpt',
                    'influencer'
                ),
                Forms\Components\RichEditor::make('bio')->label('السيرة الكاملة')->columnSpanFull(),
            ]),

            Forms\Components\Section::make('إحصائية إضافية')->schema([
                Forms\Components\TextInput::make('stat')->label('الرقم / الإحصاء')
                    ->placeholder('مثال: 50M+'),
                Forms\Components\TextInput::make('stat_label')->label('وصف الإحصاء')
                    ->placeholder('مشاهدة شهرياً'),
            ])->columns(2)->collapsed(),

            SeoFields::section('influencer'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageUpload::column(),
                Tables\Columns\TextColumn::make('name')->label('الاسم')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('handle')->label('المعرّف')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('platform')->label('المنصة')->badge()->toggleable(),
                Tables\Columns\TextColumn::make('followers')->label('المتابعون')->toggleable(),
                Tables\Columns\TextColumn::make('country')->label('الدولة')->toggleable(),
                Tables\Columns\IconColumn::make('featured')->label('مميز')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->label('التاريخ')->dateTime('Y-m-d')->sortable()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('platform')->label('المنصة')->options(fn () =>
                    Person::query()->where('category', 'influencer')
                        ->whereNotNull('platform')->distinct()->pluck('platform', 'platform')->toArray()
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
            'index'  => Pages\ListInfluencers::route('/'),
            'create' => Pages\CreateInfluencer::route('/create'),
            'edit'   => Pages\EditInfluencer::route('/{record}/edit'),
        ];
    }
}
