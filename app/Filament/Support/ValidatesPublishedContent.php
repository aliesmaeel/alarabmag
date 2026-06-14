<?php

namespace App\Filament\Support;

use App\Support\PublishValidation;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

trait ValidatesPublishedContent
{
    /** @param array<string, mixed> $data */
    protected function validatePublishedBody(array $data, string $field = 'body'): void
    {
        if (($data['status'] ?? '') !== 'published') {
            return;
        }

        if ($error = PublishValidation::validateBodyForPublish($data[$field] ?? null)) {
            Notification::make()->title($error)->danger()->send();
            throw ValidationException::withMessages([$field => $error]);
        }
    }

    /** @param array<string, mixed> $data */
    protected function validatePublishedBio(array $data, string $field = 'bio'): void
    {
        if ($error = PublishValidation::validateBioForPublish($data[$field] ?? null)) {
            Notification::make()->title($error)->danger()->send();
            throw ValidationException::withMessages([$field => $error]);
        }
    }

    /** @param array<string, mixed> $data */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->validatePublishedBody($data);

        return parent::mutateFormDataBeforeCreate($data);
    }

    /** @param array<string, mixed> $data */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->validatePublishedBody($data);

        return parent::mutateFormDataBeforeSave($data);
    }
}

trait ValidatesPersonBio
{
    /** @param array<string, mixed> $data */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->validatePublishedBio($data);

        return parent::mutateFormDataBeforeCreate($data);
    }

    /** @param array<string, mixed> $data */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->validatePublishedBio($data);

        return parent::mutateFormDataBeforeSave($data);
    }

    /** @param array<string, mixed> $data */
    protected function validatePublishedBio(array $data, string $field = 'bio'): void
    {
        if ($error = PublishValidation::validateBioForPublish($data[$field] ?? null)) {
            \Filament\Notifications\Notification::make()->title($error)->danger()->send();
            throw ValidationException::withMessages([$field => $error]);
        }
    }
}
