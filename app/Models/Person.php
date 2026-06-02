<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Person extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'name_en', 'role', 'category',
        'country', 'flag', 'image_url',
        'excerpt', 'bio',
        'stat', 'stat_label',
        'handle', 'platform', 'followers',
        'hospital', 'specialty', 'badge',
        'company', 'net_worth',
        'featured',
        'meta_title', 'meta_description', 'meta_keywords',
        'og_title', 'og_description', 'og_image',
    ];

    protected $casts = [
        'featured' => 'boolean',
    ];

    // Scopes
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }
}
