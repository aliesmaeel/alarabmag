<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Filament\Support\AiAssist;
use App\Filament\Support\ImageUpload;
use App\Filament\Support\SeoFields;
use App\Models\Article;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $slug = 'news';

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationLabel = 'الأخبار';
    protected static ?string $modelLabel = 'خبر';
    protected static ?string $pluralModelLabel = 'الأخبار';
    protected static ?string $navigationGroup = 'المحتوى';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('المحتوى')
                ->headerActions([
                    AiAssist::generateFullArticleAction('article'),
                    AiAssist::fillExcerptAction('article'),
                ])
                ->schema([
                AiAssist::apply(
                    Forms\Components\TextInput::make('title')
                        ->label('العنوان')
                        ->required()
                        ->maxLength(1000)
                        ->columnSpanFull(),
                    'title',
                    'article'
                ),
                AiAssist::apply(
                    Forms\Components\TextInput::make('subtitle')
                        ->label('العنوان الفرعي')
                        ->maxLength(500)
                        ->columnSpanFull(),
                    'subtitle',
                    'article'
                ),
                AiAssist::apply(
                    Forms\Components\Textarea::make('excerpt')
                        ->label('المقتطف')
                        ->rows(3)
                        ->columnSpanFull(),
                    'excerpt',
                    'article'
                ),
                Forms\Components\RichEditor::make('body')
                    ->label('النص الكامل')
                    ->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('التصنيف والمعلومات')->schema([
                Forms\Components\TextInput::make('category')
                    ->label('القسم')
                    ->required()
                    ->datalist(['عام', 'سياسة', 'اقتصاد', 'رياضة', 'ثقافة', 'تكنولوجيا', 'صحة', 'فن'])
                    ->default('عام'),
                Forms\Components\TextInput::make('author')
                    ->label('الكاتب')
                    ->required()
                    ->default('فريق التحرير'),
                Forms\Components\TextInput::make('region')
                    ->label('المنطقة')
                    ->maxLength(100),
                Forms\Components\TextInput::make('read_time')
                    ->label('مدة القراءة')
                    ->default('5 دقائق'),
                Forms\Components\Select::make('status')
                    ->label('الحالة')
                    ->options(['published' => 'منشور', 'draft' => 'مسودة'])
                    ->required()
                    ->default('published')
                    ->native(false),
                Forms\Components\Toggle::make('featured')
                    ->label('مميز'),
                Forms\Components\Toggle::make('in_ticker')
                    ->label('عرض في شريط العاجل')
                    ->helperText('يظهر العنوان في الشريط المتحرك أعلى الموقع')
                    ->live(),
                Forms\Components\TextInput::make('ticker_order')
                    ->label('ترتيب الشريط')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->visible(fn (Forms\Get $get) => (bool) $get('in_ticker')),
            ])->columns(2),

            Forms\Components\Section::make('الصورة')->schema([
                ImageUpload::make('image_url', 'صورة الخبر'),
            ]),

            SeoFields::section('article'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageUpload::column(),
                Tables\Columns\TextColumn::make('title')->label('العنوان')->searchable()->limit(60)->wrap(),
                Tables\Columns\TextColumn::make('category')->label('القسم')->badge()->searchable(),
                Tables\Columns\TextColumn::make('author')->label('الكاتب')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('region')->label('المنطقة')->toggleable(),
                Tables\Columns\IconColumn::make('featured')->label('مميز')->boolean(),
                Tables\Columns\IconColumn::make('in_ticker')->label('الشريط')->boolean()->toggleable(),
                Tables\Columns\TextColumn::make('status')->label('الحالة')->badge()
                    ->color(fn (string $state): string => $state === 'published' ? 'success' : 'gray')
                    ->formatStateUsing(fn (string $state) => $state === 'published' ? 'منشور' : 'مسودة'),
                Tables\Columns\TextColumn::make('views')->label('المشاهدات')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('تاريخ النشر')->dateTime('Y-m-d H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('الحالة')
                    ->options(['published' => 'منشور', 'draft' => 'مسودة']),
                SelectFilter::make('category')->label('القسم')
                    ->options(fn () => Article::query()->distinct()->pluck('category', 'category')->toArray()),
                TernaryFilter::make('featured')->label('مميز فقط'),
                TernaryFilter::make('in_ticker')->label('في شريط العاجل'),
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
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
