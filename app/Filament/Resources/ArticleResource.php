<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\TranslatesResourceLabels;
use App\Filament\Resources\ArticleResource\Pages;
use App\Filament\Support\AiAssist;
use App\Filament\Support\ImageUpload;
use App\Filament\Support\SeoFields;
use App\Models\Article;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class ArticleResource extends Resource
{
    use TranslatesResourceLabels;

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
            Forms\Components\Section::make(__('المحتوى'))
                ->headerActions([
                    AiAssist::generateFullArticleAction('article'),
                    AiAssist::fillExcerptAction('article'),
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
                                $set('slug', Article::uniqueSlug($state));
                            }
                        })
                        ->columnSpanFull(),
                    'title',
                    'article'
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
                            ->action(function (Forms\Get $get, Set $set, ?Article $record): void {
                                $title = trim((string) $get('title'));

                                if (blank($title)) {
                                    \Filament\Notifications\Notification::make()
                                        ->warning()->title(__('أدخل العنوان أولاً'))->send();

                                    return;
                                }

                                $set('slug', Article::uniqueSlug($title, $record?->id));
                            })
                    )
                    ->columnSpanFull(),
                AiAssist::apply(
                    Forms\Components\TextInput::make('subtitle')
                        ->label(__('العنوان الفرعي'))
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

            Forms\Components\Section::make(__('التصنيف والمعلومات'))->schema([
                Forms\Components\TextInput::make('category')
                    ->label(__('القسم'))
                    ->required()
                    ->datalist(['عام', 'سياسة', 'اقتصاد', 'رياضة', 'ثقافة', 'تكنولوجيا', 'صحة', 'فن'])
                    ->default('عام'),
                Forms\Components\TextInput::make('author')
                    ->label(__('الكاتب'))
                    ->required()
                    ->default('فريق التحرير'),
                Forms\Components\TextInput::make('region')
                    ->label(__('المنطقة'))
                    ->maxLength(100),
                Forms\Components\TextInput::make('read_time')
                    ->label(__('مدة القراءة'))
                    ->default('5 دقائق'),
                Forms\Components\Select::make('status')
                    ->label(__('الحالة'))
                    ->options(['published' => __('منشور'), 'draft' => __('مسودة')])
                    ->required()
                    ->default('published')
                    ->native(false),
                Forms\Components\Toggle::make('featured')
                    ->label(__('مميز')),
                Forms\Components\Toggle::make('in_ticker')
                    ->label(__('عرض في شريط العاجل'))
                    ->helperText(__('يظهر العنوان في الشريط المتحرك أعلى الموقع'))
                    ->live(),
                Forms\Components\TextInput::make('ticker_order')
                    ->label(__('ترتيب الشريط'))
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->visible(fn (Forms\Get $get) => (bool) $get('in_ticker')),
            ])->columns(2),

            Forms\Components\Section::make(__('الصورة'))->schema([
                ImageUpload::make('image_url', 'صورة الخبر', '16:9')
                    ->helperText(__('تُقصّ الصورة تلقائيًا بنسبة 16:9 لتظهر بشكل مثالي في صفحة الخبر على الجوال والويب. استخدم زر التحرير ✎ لتحديد الجزء المناسب من الصورة قبل الحفظ.')),
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
                Tables\Columns\TextColumn::make('title')->label(__('العنوان'))->searchable()->limit(60)->wrap(),
                Tables\Columns\TextColumn::make('slug')->label(__('الرابط'))->searchable()->limit(40)->toggleable(),
                Tables\Columns\TextColumn::make('category')->label(__('القسم'))->badge()->searchable(),
                Tables\Columns\TextColumn::make('author')->label(__('الكاتب'))->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('region')->label(__('المنطقة'))->toggleable(),
                Tables\Columns\IconColumn::make('featured')->label(__('مميز'))->boolean(),
                Tables\Columns\IconColumn::make('in_ticker')->label(__('الشريط'))->boolean()->toggleable(),
                Tables\Columns\TextColumn::make('status')->label(__('الحالة'))->badge()
                    ->color(fn (string $state): string => $state === 'published' ? 'success' : 'gray')
                    ->formatStateUsing(fn (string $state) => $state === 'published' ? __('منشور') : __('مسودة')),
                Tables\Columns\TextColumn::make('views')->label(__('المشاهدات'))->numeric()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label(__('تاريخ النشر'))->dateTime('Y-m-d H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label(__('الحالة'))
                    ->options(['published' => __('منشور'), 'draft' => __('مسودة')]),
                SelectFilter::make('category')->label(__('القسم'))
                    ->options(fn () => Article::query()->distinct()->pluck('category', 'category')->toArray()),
                TernaryFilter::make('featured')->label(__('مميز فقط')),
                TernaryFilter::make('in_ticker')->label(__('في شريط العاجل')),
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
