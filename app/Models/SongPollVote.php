<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SongPollVote extends Model
{
    protected $fillable = [
        'song_poll_id', 'song_poll_entry_id', 'email', 'voter_hash', 'ip_hash',
    ];

    public function poll(): BelongsTo
    {
        return $this->belongsTo(SongPoll::class, 'song_poll_id');
    }

    public function entry(): BelongsTo
    {
        return $this->belongsTo(SongPollEntry::class, 'song_poll_entry_id');
    }
}
