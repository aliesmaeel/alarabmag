<?php

namespace App\Models;

use App\Support\Slug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Interview extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'description', 'video_url', 'category',
        'thumbnail_url', 'featured', 'status', 'views',
        'meta_title', 'meta_description', 'meta_keywords',
        'og_title', 'og_description', 'og_image',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'views'    => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (Interview $interview) {
            if (blank($interview->slug) && filled($interview->title)) {
                $interview->slug = static::uniqueSlug($interview->title, $interview->id);
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
        return Slug::unique($title, self::class, $ignoreId, 'interview');
    }
}
