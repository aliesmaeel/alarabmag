<?php

namespace App\Models;

use App\Support\Slug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SongPoll extends Model
{
    protected $fillable = [
        'slug', 'title', 'title_en', 'eyebrow', 'subtitle',
        'year', 'status', 'starts_at', 'ends_at',
    ];

    protected $casts = [
        'year' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (SongPoll $poll) {
            if (blank($poll->slug) && filled($poll->title)) {
                $poll->slug = static::uniqueSlug($poll->title, $poll->id);
            }
        });

        static::deleting(function (SongPoll $poll) {
            $poll->entries()->each(fn (SongPollEntry $entry) => $entry->delete());
        });
    }

    public function entries(): HasMany
    {
        return $this->hasMany(SongPollEntry::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(SongPollVote::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeOpen($query)
    {
        return $query->published()
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()));
    }

    public function isOpen(): bool
    {
        if ($this->status !== 'published') {
            return false;
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->ends_at && $this->ends_at->isPast()) {
            return false;
        }

        return true;
    }

    public function totalVotes(): int
    {
        return (int) $this->entries()->sum('votes_count');
    }

    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        return Slug::unique($title, static::class, $ignoreId, 'poll');
    }
}
