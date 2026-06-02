<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogResource\Pages;
use App\Filament\Support\AiAssist;
use App\Filament\Support\ImageUpload;
use App\Filament\Support\SeoFields;
use App\Models\Blog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    protected static ?string $navigationLabel = 'المدونة';
    protected static ?string $modelLabel = 'تدوينة';
    protected static ?string $pluralModelLabel = 'التدوينات';
    protected static ?string $navigationGroup = 'المحتوى';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('المحتوى')
                ->headerActions([
                    AiAssist::fillExcerptAction('blog'),
                ])
                ->schema([
                AiAssist::apply(
                    Forms\Components\TextInput::make('title')->label('العنوان')->required()->maxLength(1000)->columnSpanFull(),
                    'title',
                    'blog'
                ),
                AiAssist::apply(
                    Forms\Components\Textarea::make('excerpt')->label('المقتطف')->rows(3)->columnSpanFull(),
                    'excerpt',
                    'blog'
                ),
                Forms\Components\RichEditor::make('body')->label('النص الكامل')->columnSpanFull(),
            ]),

            Forms\Components\Section::make('بيانات الكاتب')->schema([
                Forms\Components\TextInput::make('author')->label('اسم الكاتب')->required()->default('فريق التحرير'),
                Forms\Components\TextInput::make('author_bio')->label('نبذة عن الكاتب')->maxLength(500),
                ImageUpload::make('author_img', 'صورة الكاتب'),
            ])->columns(2),

            Forms\Components\Section::make('الإعدادات')->schema([
                Forms\Components\TextInput::make('tags')->label('الوسوم (مفصولة بفواصل)')->maxLength(500),
                Forms\Components\Select::make('status')->label('الحالة')
                    ->options(['published' => 'منشور', 'draft' => 'مسودة'])
                    ->required()->default('published')->native(false),
                Forms\Components\Toggle::make('featured')->label('مميز'),
                ImageUpload::make('image_url', 'صورة الغلاف')->columnSpanFull(),
            ])->columns(2),

            SeoFields::section('blog'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageUpload::column(),
                Tables\Columns\TextColumn::make('title')->label('العنوان')->searchable()->limit(60)->wrap(),
                Tables\Columns\TextColumn::make('author')->label('الكاتب')->searchable(),
                Tables\Columns\TextColumn::make('tags')->label('الوسوم')->limit(40)->toggleable(),
                Tables\Columns\IconColumn::make('featured')->label('مميز')->boolean(),
                Tables\Columns\TextColumn::make('status')->label('الحالة')->badge()
                    ->color(fn (string $state): string => $state === 'published' ? 'success' : 'gray')
                    ->formatStateUsing(fn (string $state) => $state === 'published' ? 'منشور' : 'مسودة'),
                Tables\Columns\TextColumn::make('views')->label('المشاهدات')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('التاريخ')->dateTime('Y-m-d H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('الحالة')
                    ->options(['published' => 'منشور', 'draft' => 'مسودة']),
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
            'index' => Pages\ListBlogs::route('/'),
            'create' => Pages\CreateBlog::route('/create'),
            'edit' => Pages\EditBlog::route('/{record}/edit'),
        ];
    }
}
