<?php

namespace App\Filament\Support;

use App\Services\FileUploadService;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class VideoUpload
{
    public static function make(string $name = 'video_url', string $label = 'ملف الفيديو'): FileUpload
    {
        $uploader = app(FileUploadService::class);

        return FileUpload::make($name)
            ->label($label)
            ->disk('local')
            ->directory('livewire-tmp')
            ->visibility('private')
            ->acceptedFileTypes([
                'video/mp4',
                'video/webm',
                'video/quicktime',
                'video/x-msvideo',
            ])
            ->maxSize(512000)
            ->previewable(false)
            ->downloadable(false)
            ->openable(false)
            ->fetchFileInformation(false)
            ->helperText('ارفع ملف فيديو فقط (mp4, webm, mov) — يُخزَّن على Amazon S3. حد أقصى 500 ميجابايت.')
            ->saveUploadedFileUsing(function ($file) use ($uploader) {
                $path = $uploader->uploadFile($file, 'interviews/videos');

                if ($file instanceof TemporaryUploadedFile) {
                    $file->delete();
                } elseif (is_string($file) && Storage::disk('local')->exists($file)) {
                    Storage::disk('local')->delete($file);
                }

                return $path;
            })
            ->getUploadedFileUsing(function (?string $file) use ($uploader): ?array {
                if (! filled($file)) {
                    return null;
                }

                $url = $uploader->resolveUrl($file);

                if (! $url) {
                    return null;
                }

                return [
                    'name' => basename($file),
                    'size' => 0,
                    'type' => 'video/mp4',
                    'url'  => $url,
                ];
            })
            ->deleteUploadedFileUsing(function (?string $file) use ($uploader): void {
                $uploader->deleteFile($file);
            });
    }
}
