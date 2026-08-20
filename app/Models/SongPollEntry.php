<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SongPollEntry extends Model
{
    protected $fillable = [
        'song_poll_id', 'title', 'artist', 'country', 'flag',
        'image_url', 'listen_url', 'excerpt', 'votes_count', 'sort_order',
    ];

    protected $casts = [
        'votes_count' => 'integer',
        'sort_order' => 'integer',
    ];

    public function poll(): BelongsTo
    {
        return $this->belongsTo(SongPoll::class, 'song_poll_id');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(SongPollVote::class);
    }

    public function shareOf(int $total): float
    {
        if ($total <= 0) {
            return 0;
        }

        return round(($this->votes_count / $total) * 100, 1);
    }
}
