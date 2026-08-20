<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SongPollVoteCsv
{
    public static function download(Builder $query, string $filename): StreamedResponse
    {
        $filename = str_ends_with($filename, '.csv') ? $filename : $filename.'.csv';

        return response()->streamDownload(function () use ($query): void {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['email', 'poll', 'song', 'artist', 'voted_at']);

            $query->clone()
                ->with(['poll:id,title', 'entry:id,title,artist'])
                ->reorder()
                ->orderByDesc('id')
                ->chunk(500, function ($votes) use ($handle): void {
                    foreach ($votes as $vote) {
                        fputcsv($handle, [
                            $vote->email,
                            $vote->poll?->title,
                            $vote->entry?->title,
                            $vote->entry?->artist,
                            optional($vote->created_at)?->format('Y-m-d H:i:s'),
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
