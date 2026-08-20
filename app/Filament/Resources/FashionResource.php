<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\TranslatesResourceLabels;
use App\Filament\Resources\FashionResource\Pages;
use App\Filament\Support\AiAssist;
use App\Filament\Support\ImageUpload;
use App\Filament\Support\SeoFields;
use App\Models\Article;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;

class FashionResource extends Resource
{
    use TranslatesResourceLabels;

    protected static ?string $model = Article::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationLabel = 'الموضة';
    protected static ?string $modelLabel = 'تقرير موضة';
    protected static ?string $pluralModelLabel = 'تقارير الموضة';
    protected static ?string $navigationGroup = 'المحتوى';
    protected static ?int $navigationSort = 6;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('category', 'موضة');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Hidden::make('category')->default('موضة'),

            Forms\Components\Section::make(__('المحتوى'))
                ->headerActions([
                    AiAssist::fillExcerptAction('article'),
                ])
                ->schema([
                AiAssist::apply(
                    Forms\Components\TextInput::make('title')
                        ->label(__('العنوان'))
                        ->required()
                        ->maxLength(1000)
                        ->columnSpanFull(),
                    'title',
                    'article'
                ),
                AiAssist::apply(
                    Forms\Components\TextInput::make('subtitle')
                        ->label(__('العنوان الفرعي / الكيكر'))
                        ->maxLength(500)
                        ->columnSpanFull(),
                    'subtitle',
                    'article'
                ),
                AiAssist::apply(
                    Forms\Components\Textarea::make('excerpt')
                        ->label(__('المقتطف'))
                        ->rows(3)
                        ->columnSpanFull(),
                    'excerpt',
                    'article'
                ),
                Forms\Components\RichEditor::make('body')
                    ->label(__('النص الكامل'))
                    ->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make(__('التفاصيل'))->schema([
                Forms\Components\TextInput::make('author')
                    ->label(__('الكاتب'))
                    ->required()
                    ->default('فريق التحرير'),
                Forms\Components\TextInput::make('region')
                    ->label(__('المنطقة'))
                    ->maxLength(100)
                    ->placeholder(__('السعودية، الإمارات...')),
                Forms\Components\TextInput::make('read_time')
                    ->label(__('وقت القراءة'))
                    ->default('6 دقائق'),
                Forms\Components\Select::make('status')
                    ->label(__('الحالة'))
                    ->options(['published' => __('منشور'), 'draft' => __('مسودة')])
                    ->default('published')
                    ->required(),
                Forms\Components\Toggle::make('featured')->label(__('مميز')),
                ImageUpload::make('image_url', 'صورة الغلاف')->columnSpanFull(),
            ])->columns(2),

            SeoFields::section('article'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageUpload::column(),
                Tables\Columns\TextColumn::make('title')->label(__('العنوان'))->searchable()->limit(50),
                Tables\Columns\TextColumn::make('subtitle')->label(__('الكيكر'))->limit(30)->toggleable(),
                Tables\Columns\TextColumn::make('region')->label(__('المنطقة'))->toggleable(),
                Tables\Columns\IconColumn::make('featured')->label(__('مميز'))->boolean(),
                Tables\Columns\TextColumn::make('created_at')->label(__('التاريخ'))->dateTime('Y-m-d')->sortable(),
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
            'index'  => Pages\ListFashion::route('/'),
            'create' => Pages\CreateFashion::route('/create'),
            'edit'   => Pages\EditFashion::route('/{record}/edit'),
        ];
    }
}
