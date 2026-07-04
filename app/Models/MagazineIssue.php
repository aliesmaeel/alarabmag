<?php

namespace App\Models;

use App\Support\Slug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MagazineIssue extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'html_path',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (MagazineIssue $issue) {
            if (blank($issue->slug) && filled($issue->name)) {
                $issue->slug = static::uniqueSlug($issue->name, $issue->id);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** @param  Builder<MagazineIssue>  $query */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /** @param  Builder<MagazineIssue>  $query */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('sort_order')->orderByDesc('updated_at');
    }

    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        return Slug::unique($name, self::class, $ignoreId, 'magazine');
    }
}
