<?php

namespace App\Support;

use App\Models\CreatorApplication;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CreatorApplicationCsv
{
    public static function download(Builder $query, string $filename): StreamedResponse
    {
        $filename = str_ends_with($filename, '.csv') ? $filename : $filename.'.csv';

        return response()->streamDownload(function () use ($query): void {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'name', 'email', 'country', 'phone', 'niche', 'followers',
                'social_links', 'bio', 'status', 'submitted_at',
            ]);

            $query->clone()
                ->reorder()
                ->orderByDesc('id')
                ->chunk(300, function ($applications) use ($handle): void {
                    foreach ($applications as $application) {
                        /** @var CreatorApplication $application */
                        fputcsv($handle, [
                            $application->name,
                            $application->email,
                            $application->country,
                            $application->full_phone,
                            $application->niche,
                            $application->followers,
                            implode(' | ', $application->social_link_list),
                            $application->bio,
                            $application->status_label,
                            optional($application->created_at)?->format('Y-m-d H:i:s'),
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
