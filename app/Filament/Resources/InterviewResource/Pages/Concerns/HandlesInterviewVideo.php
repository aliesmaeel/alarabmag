<?php

namespace App\Filament\Resources\InterviewResource\Pages\Concerns;

use Illuminate\Validation\ValidationException;

trait HandlesInterviewVideo
{
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
        if (blank($data['video_url'] ?? null) && property_exists($this, 'record') && $this->record) {
            $existing = $this->record->video_url;

            if (app(\App\Services\FileUploadService::class)->isS3Path($existing)) {
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
