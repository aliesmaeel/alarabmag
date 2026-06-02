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
            Stat::make('الأخبار', Article::count())
                ->description(Article::where('status', 'published')->count() . ' منشور · ' . Article::where('status', 'draft')->count() . ' مسودة')
                ->descriptionIcon('heroicon-m-newspaper')
                ->color('primary'),

            Stat::make('التدوينات', Blog::count())
                ->description(Blog::where('featured', true)->count() . ' مميزة')
                ->descriptionIcon('heroicon-m-pencil-square')
                ->color('info'),

            Stat::make('الشخصيات', Person::count())
                ->description(
                    Person::where('category', 'influencer')->count() . ' مؤثر · ' .
                    Person::where('category', 'artist')->count() . ' فنان · ' .
                    Person::where('category', 'doctor')->count() . ' طبيب · ' .
                    Person::where('category', 'business')->count() . ' أعمال'
                )
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),

            Stat::make('إجمالي المشاهدات', number_format($totalViews))
                ->description('عبر الأخبار والتدوينات')
                ->descriptionIcon('heroicon-m-eye')
                ->color('warning'),
        ];
    }
}
