<?php

namespace App\Models;

use App\Support\Slug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Article extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saving(function (Article $article) {
            if (blank($article->slug) && filled($article->title)) {
                $article->slug = static::uniqueSlug($article->title, $article->id);
            }

            if ($article->in_ticker) {
                if ((int) $article->ticker_order === 0) {
                    $query = static::query()->where('in_ticker', true);
                    if ($article->exists) {
                        $query->where('id', '!=', $article->id);
                    }
                    $article->ticker_order = (int) $query->max('ticker_order') + 1;
                }
            } else {
                $article->ticker_order = 0;
            }
        });
    }

    protected $fillable = [
        'title', 'slug', 'subtitle', 'excerpt', 'body',
        'category', 'author', 'image_url', 'read_time',
        'featured', 'in_ticker', 'ticker_order', 'status', 'region', 'views',
        'meta_title', 'meta_description', 'meta_keywords',
        'og_title', 'og_description', 'og_image',
    ];

    protected $casts = [
        'featured'   => 'boolean',
        'in_ticker'  => 'boolean',
        'ticker_order' => 'integer',
        'views'      => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeInTicker($query)
    {
        return $query->where('in_ticker', true);
    }

    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }

    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        return Slug::unique($title, self::class, $ignoreId, 'news');
    }
}
