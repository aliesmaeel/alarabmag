<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class FileUploadService
{
    public const VIDEO_MIME_TYPES = [
        'video/mp4',
        'video/webm',
        'video/quicktime',
        'video/x-msvideo',
    ];

    public const VIDEO_MAX_BYTES = 524_288_000;

    public function storageDisk(): string
    {
        $type = trim((string) env('STORAGE_TYPE', 's3'), " \t\n\r\0\x0B'\"");

        return in_array($type, ['public', 's3', 'uploads'], true) ? $type : 's3';
    }

    /**
     * Upload a file to local public storage or Amazon S3.
     *
     * @param  UploadedFile|string  $file  Uploaded file or raw file contents
     * @param  string  $path  Directory path, e.g. "interviews/videos"
     * @return string Relative path stored in the database (e.g. interviews/videos/file.mp4)
     */
    public function uploadFile($file, string $path, bool $returnWithPath = true): string
    {
        $storageType = $this->storageDisk();
        $directory = trim($path, '/');

        if ($directory === '') {
            throw new RuntimeException('Upload path cannot be empty.');
        }

        $destination = $storageType === 'public'
            ? $directory
            : 'storage/'.$directory;

        $disk = Storage::disk($storageType);

        if ($file instanceof UploadedFile) {
            $extension = $file->getClientOriginalExtension() ?: 'mp4';
            $slug = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $basename = time().'-'.($slug !== '' ? $slug : Str::random(8)).'.'.$extension;

            $storedPath = $disk->putFileAs($destination, $file, $basename);
        } else {
            $basename = time().'-'.Str::random(8).'.bin';
            $storedPath = $disk->put($destination.'/'.$basename, $file);
        }

        if (! is_string($storedPath) || trim($storedPath) === '') {
            throw new RuntimeException('Failed to upload file to '.$storageType.' storage.');
        }

        $storedPath = trim($storedPath, '/');

        if ($returnWithPath) {
            if ($storageType === 's3' && str_starts_with($storedPath, 'storage/')) {
                return substr($storedPath, strlen('storage/'));
            }

            if ($storageType === 'public' && str_contains($storedPath, $directory)) {
                return $directory.'/'.basename($storedPath);
            }

            return $storedPath;
        }

        return basename($storedPath);
    }

    /**
     * @return array{upload_url: string, path: string, headers: array<string, string>}
     */
    public function createPresignedVideoUpload(string $originalName, string $contentType): array
    {
        if ($this->storageDisk() !== 's3') {
            throw new RuntimeException('Direct S3 upload requires STORAGE_TYPE=s3.');
        }

        if (! in_array($contentType, self::VIDEO_MIME_TYPES, true)) {
            throw new RuntimeException('Unsupported video type.');
        }

        $relativePath = 'interviews/videos/'.$this->videoBasename($originalName);
        $s3Key = 'storage/'.$relativePath;

        $client = Storage::disk('s3')->getClient();
        $command = $client->getCommand('PutObject', [
            'Bucket' => config('filesystems.disks.s3.bucket'),
            'Key' => $s3Key,
            'ContentType' => $contentType,
        ]);

        $presignedRequest = $client->createPresignedRequest($command, '+30 minutes');

        return [
            'upload_url' => (string) $presignedRequest->getUri(),
            'path' => $relativePath,
            'headers' => [
                'Content-Type' => $contentType,
            ],
        ];
    }

    public function confirmVideoUpload(string $path): bool
    {
        $key = $this->s3ObjectKey($path);

        return filled($key) && Storage::disk('s3')->exists($key);
    }

    /**
     * @return list<string>
     */
    public function uploadCorsOrigins(): array
    {
        $origins = array_filter(array_map(
            fn (string $origin) => rtrim(trim($origin), '/'),
            explode(',', (string) env('S3_UPLOAD_CORS_ORIGINS', ''))
        ));

        foreach ([config('app.url'), env('APP_URL')] as $appUrl) {
            if (! filled($appUrl)) {
                continue;
            }

            $origin = rtrim(trim((string) $appUrl), '/');

            if (preg_match('#^https?://#i', $origin)) {
                $origins[] = $origin;
            }
        }

        foreach ($origins as $origin) {
            if (preg_match('#^https://(www\.)?([^/]+)$#i', $origin, $matches)) {
                $host = $matches[2];
                $origins[] = 'https://'.$host;
                $origins[] = 'https://www.'.$host;
            }
        }

        $origins[] = 'http://localhost:8000';
        $origins[] = 'http://127.0.0.1:8000';

        return array_values(array_unique(array_filter($origins)));
    }

    /**
     * @param  array<string, mixed>  $rule
     */
    public function applyUploadCors(array $rule): void
    {
        if ($this->storageDisk() !== 's3') {
            throw new RuntimeException('Direct S3 upload requires STORAGE_TYPE=s3.');
        }

        $bucket = (string) config('filesystems.disks.s3.bucket');

        if ($bucket === '') {
            throw new RuntimeException('AWS_BUCKET is not configured.');
        }

        Storage::disk('s3')->getClient()->putBucketCors([
            'Bucket' => $bucket,
            'CORSConfiguration' => [
                'CORSRules' => [$rule],
            ],
        ]);
    }

    public function resolveUrl(?string $value): ?string
    {
        if (! filled($value)) {
            return null;
        }

        $value = trim($value);

        if ($value === '') {
            return null;
        }

        if (str_starts_with($value, 'data:')) {
            return $value;
        }

        if (preg_match('#^https?://#i', $value)) {
            return $this->isExternalVideoUrl($value) ? null : $value;
        }

        if (str_starts_with($value, '/uploads/') || str_starts_with($value, 'uploads/')) {
            return url(ltrim($value, '/'));
        }

        $disk = $this->storageDisk();

        if ($disk === 's3') {
            $key = str_starts_with($value, 'storage/')
                ? $value
                : 'storage/'.ltrim($value, '/');

            if (trim($key, '/') === '' || trim($key, '/') === 'storage') {
                return null;
            }

            return $this->s3PlaybackUrl($key);
        }

        if ($disk === 'uploads') {
            return url(ltrim($value, '/'));
        }

        return Storage::disk($disk)->url(ltrim($value, '/'));
    }

    public function deleteFile(?string $value): void
    {
        if (! filled($value) || preg_match('#^https?://#i', $value)) {
            return;
        }

        $disk = $this->storageDisk();
        $key = $disk === 's3'
            ? (str_starts_with($value, 'storage/') ? $value : 'storage/'.ltrim($value, '/'))
            : ltrim($value, '/');

        if (trim($key, '/') === '' || trim($key, '/') === 'storage') {
            return;
        }

        if (Storage::disk($disk)->exists($key)) {
            Storage::disk($disk)->delete($key);
        }
    }

    public function isExternalVideoUrl(?string $value): bool
    {
        if (! filled($value)) {
            return false;
        }

        return (bool) preg_match('#^https?://(www\.)?(youtube\.com|youtu\.be|vimeo\.com)#i', trim($value));
    }

    public function isS3Path(?string $value): bool
    {
        if (! filled($value)) {
            return false;
        }

        return ! preg_match('#^https?://#i', trim($value));
    }

    public function playbackUrl(?string $value, ?string $streamRoute = null): ?string
    {
        if (! filled($value) || $this->isExternalVideoUrl($value)) {
            return null;
        }

        if ($this->isS3Path($value) && $this->storageDisk() === 's3' && $streamRoute) {
            return $streamRoute;
        }

        return $this->resolveUrl($value);
    }

    public function s3ObjectKey(?string $value): ?string
    {
        if (! filled($value) || $this->isExternalVideoUrl($value) || ! $this->isS3Path($value)) {
            return null;
        }

        $key = str_starts_with($value, 'storage/')
            ? $value
            : 'storage/'.ltrim($value, '/');

        return trim($key, '/') !== '' && trim($key, '/') !== 'storage' ? $key : null;
    }

    protected function videoBasename(string $originalName): string
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION) ?: 'mp4');
        $slug = Str::slug(pathinfo($originalName, PATHINFO_FILENAME));

        return time().'-'.($slug !== '' ? $slug : Str::random(8)).'.'.$extension;
    }

    protected function s3PlaybackUrl(string $key): string
    {
        $disk = Storage::disk('s3');

        try {
            return $disk->temporaryUrl($key, now()->addHours(12));
        } catch (Throwable) {
            return $disk->url($key);
        }
    }
}
