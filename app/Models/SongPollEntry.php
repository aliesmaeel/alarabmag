<?php

namespace App\Models;

use App\Services\FileUploadService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SongPollEntry extends Model
{
    protected $fillable = [
        'song_poll_id', 'title', 'artist', 'country', 'flag',
        'image_url', 'listen_url', 'audio_path', 'excerpt', 'votes_count', 'sort_order',
    ];

    protected $casts = [
        'votes_count' => 'integer',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::updating(function (SongPollEntry $entry) {
            $files = app(FileUploadService::class);

            if ($entry->isDirty('audio_path')) {
                $files->deleteLocalUpload($entry->getOriginal('audio_path'));
            }

            if ($entry->isDirty('image_url')) {
                $files->deleteLocalUpload($entry->getOriginal('image_url'));
            }
        });

        static::deleting(function (SongPollEntry $entry) {
            $files = app(FileUploadService::class);
            $files->deleteLocalUpload($entry->audio_path);
            $files->deleteLocalUpload($entry->image_url);
        });
    }

    public function poll(): BelongsTo
    {
        return $this->belongsTo(SongPoll::class, 'song_poll_id');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(SongPollVote::class);
    }

    public function audioUrl(): ?string
    {
        if (! filled($this->audio_path)) {
            return null;
        }

        $relative = ltrim((string) $this->audio_path, '/');

        if (str_starts_with($relative, 'uploads/')) {
            return route('vote.audio', $this);
        }

        return app(FileUploadService::class)->resolveUrl($this->audio_path);
    }

    public function shareOf(int $total): float
    {
        if ($total <= 0) {
            return 0;
        }

        return round(($this->votes_count / $total) * 100, 1);
    }
}
