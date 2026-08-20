<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubscriberCsv
{
    public static function download(Builder $query, string $filename): StreamedResponse
    {
        $filename = str_ends_with($filename, '.csv') ? $filename : $filename.'.csv';

        return response()->streamDownload(function () use ($query): void {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['email', 'source', 'subscribed_at']);

            $query->clone()
                ->reorder()
                ->orderByDesc('id')
                ->chunk(500, function ($subscribers) use ($handle): void {
                    foreach ($subscribers as $subscriber) {
                        fputcsv($handle, [
                            $subscriber->email,
                            $subscriber->source,
                            optional($subscriber->created_at)?->format('Y-m-d H:i:s'),
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
