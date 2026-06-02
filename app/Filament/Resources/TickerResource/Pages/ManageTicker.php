<?php

namespace App\Filament\Resources\TickerResource\Pages;

use App\Filament\Resources\ArticleResource;
use App\Filament\Resources\TickerResource;
use App\Models\Article;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ManageTicker extends ListRecords
{
    protected static string $resource = TickerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('addArticle')
                ->label('إضافة خبر للشريط')
                ->icon('heroicon-o-plus')
                ->form([
                    Select::make('article_id')
                        ->label('اختر خبراً منشوراً')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->options(fn () => Article::query()
                            ->where('status', 'published')
                            ->where('in_ticker', false)
                            ->orderByDesc('created_at')
                            ->limit(100)
                            ->pluck('title', 'id')
                            ->toArray())
                        ->getSearchResultsUsing(fn (string $search) => Article::query()
                            ->where('status', 'published')
                            ->where('in_ticker', false)
                            ->where('title', 'like', "%{$search}%")
                            ->orderByDesc('created_at')
                            ->limit(50)
                            ->pluck('title', 'id')
                            ->toArray())
                        ->getOptionLabelUsing(fn ($value): ?string => Article::find($value)?->title),
                ])
                ->action(function (array $data): void {
                    $article = Article::findOrFail($data['article_id']);
                    $maxOrder = (int) Article::where('in_ticker', true)->max('ticker_order');

                    $article->update([
                        'in_ticker' => true,
                        'ticker_order' => $maxOrder + 1,
                    ]);

                    Notification::make()
                        ->title('تمت إضافة الخبر إلى شريط العاجل')
                        ->success()
                        ->send();
                }),
            Actions\Action::make('newArticle')
                ->label('خبر جديد')
                ->icon('heroicon-o-document-plus')
                ->url(ArticleResource::getUrl('create')),
        ];
    }
}
