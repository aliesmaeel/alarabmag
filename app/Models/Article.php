<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'subtitle', 'excerpt', 'body',
        'category', 'author', 'image_url', 'read_time',
        'featured', 'status', 'region', 'views',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'views'    => 'integer',
    ];

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    // Increment views
    public function incrementViews(): void
    {
        $this->increment('views');
    }
}
