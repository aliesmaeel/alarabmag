<?php

namespace App\Filament\Resources\InterviewResource\Pages\Concerns;

use App\Services\FileUploadService;
use App\Services\YouTubeService;
use Illuminate\Validation\ValidationException;

trait HandlesInterviewVideo
{
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['video_source'] = app(YouTubeService::class)->isYouTubeUrl($data['video_url'] ?? null)
            ? 'youtube'
            : 's3';

        return $data;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->ensureVideoUploaded($data);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->ensureVideoUploaded($data);
    }

    protected function ensureVideoUploaded(array $data): array
    {
        $youtube = app(YouTubeService::class);
        $files = app(FileUploadService::class);
        $source = $data['video_source'] ?? (
            $youtube->isYouTubeUrl($data['video_url'] ?? null) ? 'youtube' : 's3'
        );

        unset($data['video_source']);

        if ($source === 'youtube') {
            $canonical = $youtube->canonicalUrl($data['video_url'] ?? null);

            if (! $canonical) {
                throw ValidationException::withMessages([
                    'video_url' => 'يرجى إدخال رابط يوتيوب صالح.',
                ]);
            }

            $data['video_url'] = $canonical;

            if (blank($data['thumbnail_url'] ?? null)) {
                $data['thumbnail_url'] = $youtube->thumbnailUrl($canonical);
            }

            return $data;
        }

        if (blank($data['video_url'] ?? null) && property_exists($this, 'record') && $this->record) {
            $existing = $this->record->video_url;

            if ($files->isS3Path($existing) && ! $youtube->isYouTubeUrl($existing)) {
                $data['video_url'] = $existing;
            }
        }

        if (blank($data['video_url'] ?? null)) {
            throw ValidationException::withMessages([
                'video_url' => 'يرجى رفع ملف فيديو.',
            ]);
        }

        return $data;
    }
}
