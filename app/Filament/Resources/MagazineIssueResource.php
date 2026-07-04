<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MagazineIssueResource\Pages;
use App\Filament\Support\HtmlUpload;
use App\Models\MagazineIssue;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\TernaryFilter;

class MagazineIssueResource extends Resource
{
    protected static ?string $model = MagazineIssue::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'المجلة';

    protected static ?string $modelLabel = 'عدد';

    protected static ?string $pluralModelLabel = 'أعداد المجلة';

    protected static ?string $navigationGroup = 'المحتوى';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('بيانات العدد')->schema([
                Forms\Components\TextInput::make('name')
                    ->label('اسم العدد')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('مثال: العدد الثاني عشر · ربيع 2026')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (?string $state, Set $set, ?string $operation): void {
                        if ($operation === 'create' && filled($state)) {
                            $set('slug', MagazineIssue::uniqueSlug($state));
                        }
                    })
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('slug')
                    ->label('الرابط (slug)')
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->placeholder('يُنشأ تلقائياً من الاسم')
                    ->helperText('يُولَّد تلقائياً من اسم العدد.')
                    ->suffixAction(
                        Forms\Components\Actions\Action::make('generate_slug')
                            ->icon('heroicon-m-arrow-path')
                            ->tooltip('توليد الرابط من الاسم')
                            ->action(function (Forms\Get $get, Set $set, ?MagazineIssue $record): void {
                                $name = trim((string) $get('name'));

                                if (blank($name)) {
                                    \Filament\Notifications\Notification::make()
                                        ->warning()->title('أدخل اسم العدد أولاً')->send();

                                    return;
                                }

                                $set('slug', MagazineIssue::uniqueSlug($name, $record?->id));
                            })
                    )
                    ->columnSpanFull(),
                HtmlUpload::make('html_path', 'ملف HTML')
                    ->required(fn (string $operation): bool => $operation === 'create'),
            ]),

            Forms\Components\Section::make('الإعدادات')->schema([
                Forms\Components\Toggle::make('is_published')
                    ->label('منشور')
                    ->default(true),
                Forms\Components\TextInput::make('sort_order')
                    ->label('ترتيب العرض')
                    ->numeric()
                    ->default(0)
                    ->helperText('الأعداد ذات الرقم الأعلى تظهر أولاً.'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم العدد')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('الرابط')
                    ->searchable()
                    ->limit(40)
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('منشور')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('الترتيب')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('آخر تحديث')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_published')->label('منشور'),
            ])
            ->actions([
                Tables\Actions\Action::make('preview')
                    ->label('معاينة')
                    ->icon('heroicon-o-eye')
                    ->url(fn (MagazineIssue $record): string => route('magazine.show', $record))
                    ->openUrlInNewTab()
                    ->visible(fn (MagazineIssue $record): bool => $record->is_published && filled($record->html_path)),
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
            'index' => Pages\ListMagazineIssues::route('/'),
            'create' => Pages\CreateMagazineIssue::route('/create'),
            'edit' => Pages\EditMagazineIssue::route('/{record}/edit'),
        ];
    }
}
