<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InterviewResource\Pages;
use App\Filament\Support\ImageUpload;
use App\Filament\Support\SeoFields;
use App\Models\Interview;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class InterviewResource extends Resource
{
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
            Forms\Components\Section::make('المحتوى')->schema([
                Forms\Components\TextInput::make('title')
                    ->label('العنوان')
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
                    ->label('الرابط (slug)')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('يُستخدم في رابط الصفحة: /interviews/your-slug')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('description')
                    ->label('الوصف')
                    ->rows(4)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('video_url')
                    ->label('رابط الفيديو')
                    ->required()
                    ->url()
                    ->helperText('YouTube أو Vimeo أو رابط فيديو مباشر (.mp4)')
                    ->columnSpanFull(),
            ]),

            Forms\Components\Section::make('التصنيف والإعدادات')->schema([
                Forms\Components\TextInput::make('category')
                    ->label('التصنيف')
                    ->required()
                    ->datalist(['عام', 'أعمال', 'فنانون', 'مؤثرون', 'أطباء', 'ثقافة', 'رياضة', 'سياسة'])
                    ->default('عام'),
                Forms\Components\Select::make('status')
                    ->label('الحالة')
                    ->options(['published' => 'منشور', 'draft' => 'مسودة'])
                    ->required()
                    ->default('published')
                    ->native(false),
                Forms\Components\Toggle::make('featured')
                    ->label('مميز'),
                ImageUpload::make('thumbnail_url', 'صورة مصغّرة (اختياري)')
                    ->helperText('تظهر في قائمة المقابلات. إن تُركت فارغة تُستخدم صورة افتراضية.')
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
                    ->label('العنوان')
                    ->searchable()
                    ->limit(50)
                    ->wrap(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('التصنيف')
                    ->badge(),
                Tables\Columns\IconColumn::make('featured')
                    ->label('مميز')
                    ->boolean(),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'published' ? 'success' : 'gray')
                    ->formatStateUsing(fn (string $state) => $state === 'published' ? 'منشور' : 'مسودة'),
                Tables\Columns\TextColumn::make('views')
                    ->label('المشاهدات')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('التاريخ')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('التصنيف')
                    ->options(fn () => Interview::query()
                        ->distinct()
                        ->orderBy('category')
                        ->pluck('category', 'category')
                        ->all()),
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(['published' => 'منشور', 'draft' => 'مسودة']),
                TernaryFilter::make('featured')
                    ->label('مميز فقط'),
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
