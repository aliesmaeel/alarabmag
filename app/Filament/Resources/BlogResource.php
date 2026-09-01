<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\TranslatesResourceLabels;
use App\Filament\Resources\BlogResource\Pages;
use App\Filament\Support\AiAssist;
use App\Filament\Support\ImageUpload;
use App\Filament\Support\RichEditorField;
use App\Filament\Support\SeoFields;
use App\Models\Blog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class BlogResource extends Resource
{
    use TranslatesResourceLabels;

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
            Forms\Components\Section::make(__('المحتوى'))
                ->headerActions([
                    AiAssist::fillExcerptAction('blog'),
                ])
                ->schema([
                AiAssist::apply(
                    Forms\Components\TextInput::make('title')
                        ->label(__('العنوان'))
                        ->required()
                        ->maxLength(1000)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (?string $state, Set $set, ?string $operation) {
                            if ($operation === 'create' && filled($state)) {
                                $set('slug', Blog::uniqueSlug($state));
                            }
                        })
                        ->columnSpanFull(),
                    'title',
                    'blog'
                ),
                Forms\Components\TextInput::make('slug')
                    ->label(__('الرابط (slug)'))
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->placeholder(__('يُنشأ تلقائياً من العنوان'))
                    ->helperText(__('يُولَّد تلقائياً من العنوان. اتركه فارغاً لإنشائه تلقائياً، أو اضغط ↻ لإعادة توليده. يدعم العربية والإنجليزية.'))
                    ->suffixAction(
                        Forms\Components\Actions\Action::make('generate_slug')
                            ->icon('heroicon-m-arrow-path')
                            ->tooltip(__('توليد الرابط من العنوان'))
                            ->action(function (Forms\Get $get, Set $set, ?Blog $record): void {
                                $title = trim((string) $get('title'));

                                if (blank($title)) {
                                    \Filament\Notifications\Notification::make()
                                        ->warning()->title(__('أدخل العنوان أولاً'))->send();

                                    return;
                                }

                                $set('slug', Blog::uniqueSlug($title, $record?->id));
                            })
                    )
                    ->columnSpanFull(),
                AiAssist::apply(
                    Forms\Components\Textarea::make('excerpt')->label(__('المقتطف'))->rows(3)->columnSpanFull(),
                    'excerpt',
                    'blog'
                ),
                RichEditorField::make('body')->label(__('النص الكامل'))->columnSpanFull(),
            ]),

            Forms\Components\Section::make(__('بيانات الكاتب'))->schema([
                Forms\Components\TextInput::make('author')->label(__('اسم الكاتب'))->required()->default('فريق التحرير'),
                Forms\Components\TextInput::make('author_bio')->label(__('نبذة عن الكاتب'))->maxLength(500),
                ImageUpload::make('author_img', 'صورة الكاتب'),
            ])->columns(2),

            Forms\Components\Section::make(__('الإعدادات'))->schema([
                Forms\Components\TextInput::make('tags')->label(__('الوسوم (مفصولة بفواصل)'))->maxLength(500),
                Forms\Components\Select::make('status')->label(__('الحالة'))
                    ->options(['published' => __('منشور'), 'draft' => __('مسودة')])
                    ->required()->default('published')->native(false),
                Forms\Components\Toggle::make('featured')->label(__('مميز')),
                ImageUpload::make('image_url', 'صورة الغلاف', '16:9')
                    ->columnSpanFull()
                    ->helperText(__('تُقصّ صورة الغلاف تلقائياً بنسبة 16:9 لتظهر بشكل مثالي على الجوال والويب. استخدم زر التحرير ✎ لتحديد الجزء المناسب قبل الحفظ.')),
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
                Tables\Columns\TextColumn::make('title')->label(__('العنوان'))->searchable()->limit(60)->wrap(),
                Tables\Columns\TextColumn::make('slug')->label(__('الرابط'))->searchable()->limit(40)->toggleable(),
                Tables\Columns\TextColumn::make('author')->label(__('الكاتب'))->searchable(),
                Tables\Columns\TextColumn::make('tags')->label(__('الوسوم'))->limit(40)->toggleable(),
                Tables\Columns\IconColumn::make('featured')->label(__('مميز'))->boolean(),
                Tables\Columns\TextColumn::make('status')->label(__('الحالة'))->badge()
                    ->color(fn (string $state): string => $state === 'published' ? 'success' : 'gray')
                    ->formatStateUsing(fn (string $state) => $state === 'published' ? __('منشور') : __('مسودة')),
                Tables\Columns\TextColumn::make('views')->label(__('المشاهدات'))->numeric()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label(__('التاريخ'))->dateTime('Y-m-d H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label(__('الحالة'))
                    ->options(['published' => __('منشور'), 'draft' => __('مسودة')]),
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
            'index' => Pages\ListBlogs::route('/'),
            'create' => Pages\CreateBlog::route('/create'),
            'edit' => Pages\EditBlog::route('/{record}/edit'),
        ];
    }
}
