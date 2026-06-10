<?php

namespace App\View\Components\Site;

use App\Models\Article;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Ticker extends Component
{
    public string $label;

    /** @var Collection<int, array{url: string, kicker: string, title: string}> */
    public Collection $entries;

    public function __construct(?string $label = null)
    {
        $settings = Setting::getAllAsArray();
        $this->label = $label ?? ($settings['ticker_label'] ?? 'عاجل');

        $this->entries = Article::query()
            ->published()
            ->where('in_ticker', true)
            ->orderBy('ticker_order')
            ->orderByDesc('created_at')
            ->limit(20)
            ->get(['id', 'slug', 'title', 'category'])
            ->map(fn (Article $article) => [
                'url' => route('news.show', $article),
                'kicker' => $article->category ?: 'أخبار',
                'title' => $article->title,
            ]);
    }

    public function render(): View
    {
        return view('components.site.ticker-bar');
    }
}
