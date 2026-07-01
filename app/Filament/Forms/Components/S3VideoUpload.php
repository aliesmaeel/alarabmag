<?php

namespace App\Filament\Forms\Components;

use App\Services\FileUploadService;
use Filament\Forms\Components\Field;

class S3VideoUpload extends Field
{
    protected string $view = 'filament.forms.components.s3-video-upload';

    protected int $maxSizeKb = 512000;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function maxSize(int $kilobytes): static
    {
        $this->maxSizeKb = $kilobytes;

        return $this;
    }

    public function getMaxSizeKb(): int
    {
        return $this->maxSizeKb;
    }

    public function getMaxSizeBytes(): int
    {
        return $this->maxSizeKb * 1024;
    }

    public function getUploadUrl(): string
    {
        return route('admin.video-upload.store');
    }

    public function getPresignUrl(): string
    {
        return route('admin.video-upload.presign');
    }

    public function getConfirmUrl(): string
    {
        return route('admin.video-upload.confirm');
    }

    public function getAcceptedMimeTypes(): array
    {
        return FileUploadService::VIDEO_MIME_TYPES;
    }

    public function getUsesDirectS3Upload(): bool
    {
        return app(FileUploadService::class)->storageDisk() === 's3';
    }
}
