<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'excerpt', 'body',
        'author', 'author_bio', 'author_img',
        'image_url', 'tags', 'featured', 'status', 'views',
        'meta_title', 'meta_description', 'meta_keywords',
        'og_title', 'og_description', 'og_image',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'views'    => 'integer',
    ];

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function getTagsArrayAttribute(): array
    {
        if (!$this->tags) return [];
        return array_map('trim', explode(',', $this->tags));
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }
}
