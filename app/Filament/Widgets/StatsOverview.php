<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use App\Models\Blog;
use App\Models\Person;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalViews = (int) (Article::sum('views') + Blog::sum('views'));

        return [
            Stat::make(__('dashboard.news'), Article::count())
                ->description(__('dashboard.news_desc', [
                    'published' => Article::where('status', 'published')->count(),
                    'drafts' => Article::where('status', 'draft')->count(),
                ]))
                ->descriptionIcon('heroicon-m-newspaper')
                ->color('primary'),

            Stat::make(__('dashboard.posts'), Blog::count())
                ->description(__('dashboard.posts_desc', [
                    'count' => Blog::where('featured', true)->count(),
                ]))
                ->descriptionIcon('heroicon-m-pencil-square')
                ->color('info'),

            Stat::make(__('dashboard.people'), Person::count())
                ->description(__('dashboard.people_desc', [
                    'influencers' => Person::where('category', 'influencer')->count(),
                    'artists' => Person::where('category', 'artist')->count(),
                    'doctors' => Person::where('category', 'doctor')->count(),
                    'business' => Person::where('category', 'business')->count(),
                ]))
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),

            Stat::make(__('dashboard.views'), number_format($totalViews))
                ->description(__('dashboard.views_desc'))
                ->descriptionIcon('heroicon-m-eye')
                ->color('warning'),
        ];
    }
}
