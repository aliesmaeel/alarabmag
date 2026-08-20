<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\TranslatesResourceLabels;
use App\Filament\Resources\TickerResource\Pages;
use App\Models\Article;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TickerResource extends Resource
{
    use TranslatesResourceLabels;

    protected static ?string $model = Article::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';
    protected static ?string $navigationLabel = 'شريط العاجل';
    protected static ?string $modelLabel = 'خبر الشريط';
    protected static ?string $pluralModelLabel = 'شريط العاجل';
    protected static ?string $navigationGroup = 'المحتوى';
    protected static ?int $navigationSort = 0;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('in_ticker', true)
            ->orderBy('ticker_order')
            ->orderByDesc('created_at');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->label(__('العنوان'))->disabled(),
            Forms\Components\TextInput::make('category')->label(__('القسم'))->disabled(),
            Forms\Components\TextInput::make('ticker_order')->label(__('الترتيب'))->numeric()->minValue(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('ticker_order')
            ->defaultSort('ticker_order')
            ->columns([
                Tables\Columns\TextColumn::make('ticker_order')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('category')->label(__('القسم'))->badge(),
                Tables\Columns\TextColumn::make('title')->label(__('العنوان'))->searchable()->limit(70)->wrap(),
                Tables\Columns\TextColumn::make('status')->label(__('الحالة'))->badge()
                    ->color(fn (string $state): string => $state === 'published' ? 'success' : 'warning')
                    ->formatStateUsing(fn (string $state) => $state === 'published' ? __('منشور') : __('مسودة')),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\Action::make('editArticle')
                    ->label(__('تعديل الخبر'))
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn (Article $record): string => ArticleResource::getUrl('edit', ['record' => $record])),
                Tables\Actions\Action::make('removeFromTicker')
                    ->label(__('إزالة من الشريط'))
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Article $record): void {
                        $record->update(['in_ticker' => false, 'ticker_order' => 0]);
                        Notification::make()->title(__('تمت الإزالة من شريط العاجل'))->success()->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label(__('إزالة المحدد من الشريط'))
                        ->modalHeading(__('إزالة من شريط العاجل'))
                        ->modalDescription(__('لن تُحذف الأخبار، فقط تُزال من الشريط.'))
                        ->action(fn ($records) => $records->each->update(['in_ticker' => false, 'ticker_order' => 0])),
                ]),
            ])
            ->emptyStateHeading(__('لا توجد أخبار في الشريط'))
            ->emptyStateDescription(__('أضف أخباراً منشورة إلى شريط العاجل باستخدام الزر أعلاه.'))
            ->emptyStateIcon('heroicon-o-bolt');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTicker::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
