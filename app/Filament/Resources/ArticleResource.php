<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Filament\Support\ImageUpload;
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

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationLabel = 'المقالات';
    protected static ?string $modelLabel = 'مقال';
    protected static ?string $pluralModelLabel = 'المقالات';
    protected static ?string $navigationGroup = 'المحتوى';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('المحتوى')->schema([
                Forms\Components\TextInput::make('title')
                    ->label('العنوان')
                    ->required()
                    ->maxLength(1000)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('subtitle')
                    ->label('العنوان الفرعي')
                    ->maxLength(500)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('excerpt')
                    ->label('المقتطف')
                    ->rows(3)
                    ->columnSpanFull(),
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
            ])->columns(2),

            Forms\Components\Section::make('الصورة')->schema([
                ImageUpload::make('image_url', 'صورة المقال'),
            ]),
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
