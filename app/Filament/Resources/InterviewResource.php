<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\TranslatesResourceLabels;
use App\Filament\Resources\InterviewResource\Pages;
use App\Filament\Support\ImageUpload;
use App\Filament\Support\SeoFields;
use App\Filament\Support\VideoUpload;
use App\Models\Interview;
use App\Services\YouTubeService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class InterviewResource extends Resource
{
    use TranslatesResourceLabels;

    protected static ?string $model = Interview::class;

    protected static ?string $navigationIcon = 'heroicon-o-video-camera';

    protected static ?string $navigationLabel = 'المقابلات';

    protected static ?string $modelLabel = 'مقابلة';

    protected static ?string $pluralModelLabel = 'المقابلات';

    protected static ?string $navigationGroup = 'المحتوى';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('المحتوى'))->schema([
                Forms\Components\TextInput::make('title')
                    ->label(__('العنوان'))
                    ->required()
                    ->maxLength(1000)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (?string $state, Set $set, ?string $operation) {
                        if ($operation === 'create' && filled($state)) {
                            $set('slug', Interview::uniqueSlug($state));
                        }
                    })
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('slug')
                    ->label(__('الرابط (slug)'))
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText(__('يُستخدم في رابط الصفحة: /interviews/عنوان-المقابلة — يدعم العربية والإنجليزية'))
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('description')
                    ->label(__('الوصف'))
                    ->rows(4)
                    ->columnSpanFull(),
                Forms\Components\Radio::make('video_source')
                    ->label(__('مصدر الفيديو'))
                    ->options([
                        's3' => __('رفع إلى Amazon S3'),
                        'youtube' => __('رابط يوتيوب'),
                    ])
                    ->default('s3')
                    ->live()
                    ->dehydrated(false)
                    ->afterStateUpdated(fn (Set $set) => $set('video_url', null))
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('video_url')
                    ->label(__('رابط يوتيوب'))
                    ->url()
                    ->maxLength(1000)
                    ->visible(fn (Get $get): bool => $get('video_source') === 'youtube')
                    ->dehydrated(fn (Get $get): bool => $get('video_source') === 'youtube')
                    ->required(fn (Get $get): bool => $get('video_source') === 'youtube')
                    ->helperText(__('الصق رابط الفيديو من يوتيوب (يفضّل Unlisted للتضمين). يوتيوب يوفّر جودات متعددة تلقائياً.'))
                    ->rules([
                        fn (): \Closure => function (string $attribute, mixed $value, \Closure $fail): void {
                            if (! app(YouTubeService::class)->isYouTubeUrl(is_string($value) ? $value : null)) {
                                $fail(__('يرجى إدخال رابط يوتيوب صالح (youtube.com أو youtu.be أو youtube.com/shorts).'));
                            }
                        },
                    ])
                    ->columnSpanFull(),
                VideoUpload::make('video_url', 'رفع فيديو (Amazon S3)')
                    ->visible(fn (Get $get): bool => $get('video_source') !== 'youtube')
                    ->dehydrated(fn (Get $get): bool => $get('video_source') !== 'youtube')
                    ->required(fn (Get $get): bool => $get('video_source') !== 'youtube')
                    ->columnSpanFull(),
            ]),

            Forms\Components\Section::make(__('التصنيف والإعدادات'))->schema([
                Forms\Components\TextInput::make('category')
                    ->label(__('التصنيف'))
                    ->required()
                    ->datalist(['عام', 'أعمال', 'فنانون', 'مؤثرون', 'أطباء', 'ثقافة', 'رياضة', 'سياسة'])
                    ->default('عام'),
                Forms\Components\Select::make('status')
                    ->label(__('الحالة'))
                    ->options(['published' => __('منشور'), 'draft' => __('مسودة')])
                    ->required()
                    ->default('published')
                    ->native(false),
                Forms\Components\Toggle::make('featured')
                    ->label(__('مميز')),
                ImageUpload::make('thumbnail_url', 'صورة مصغّرة (اختياري)')
                    ->helperText(__('تظهر في قائمة المقابلات. إن تُركت فارغة تُستخدم صورة افتراضية.'))
                    ->columnSpanFull(),
            ])->columns(2),

            SeoFields::section('interview'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageUpload::column('thumbnail_url'),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('العنوان'))
                    ->searchable()
                    ->limit(50)
                    ->wrap(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('category')
                    ->label(__('التصنيف'))
                    ->badge(),
                Tables\Columns\IconColumn::make('featured')
                    ->label(__('مميز'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('الحالة'))
                    ->badge()
                    ->color(fn (string $state): string => $state === 'published' ? 'success' : 'gray')
                    ->formatStateUsing(fn (string $state) => $state === 'published' ? __('منشور') : __('مسودة')),
                Tables\Columns\TextColumn::make('views')
                    ->label(__('المشاهدات'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('التاريخ'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label(__('التصنيف'))
                    ->options(fn () => Interview::query()
                        ->distinct()
                        ->orderBy('category')
                        ->pluck('category', 'category')
                        ->all()),
                SelectFilter::make('status')
                    ->label(__('الحالة'))
                    ->options(['published' => __('منشور'), 'draft' => __('مسودة')]),
                TernaryFilter::make('featured')
                    ->label(__('مميز فقط')),
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
            'index' => Pages\ListInterviews::route('/'),
            'create' => Pages\CreateInterview::route('/create'),
            'edit' => Pages\EditInterview::route('/{record}/edit'),
        ];
    }
}
