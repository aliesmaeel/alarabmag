<?php

namespace App\Models;

use App\Support\Slug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'excerpt', 'body',
        'author', 'author_bio', 'author_img',
        'image_url', 'tags', 'featured', 'status', 'views',
        'meta_title', 'meta_description', 'meta_keywords',
        'og_title', 'og_description', 'og_image',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'views'    => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (Blog $blog) {
            if (blank($blog->slug) && filled($blog->title)) {
                $blog->slug = static::uniqueSlug($blog->title, $blog->id);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function getTagsArrayAttribute(): array
    {
        if (! $this->tags) {
            return [];
        }

        return array_map('trim', explode(',', $this->tags));
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }

    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        return Slug::unique($title, self::class, $ignoreId, 'blog');
    }
}
