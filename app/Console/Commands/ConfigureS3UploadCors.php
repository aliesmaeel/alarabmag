<?php

namespace App\Console\Commands;

use App\Services\FileUploadService;
use Aws\Exception\AwsException;
use Illuminate\Console\Command;
use RuntimeException;

class ConfigureS3UploadCors extends Command
{
    protected $signature = 's3:configure-upload-cors
                            {--dry-run : Show the CORS rule without applying it}';

    protected $description = 'Apply S3 bucket CORS rules required for direct browser video uploads';

    public function handle(FileUploadService $files): int
    {
        if ($files->storageDisk() !== 's3') {
            $this->error('STORAGE_TYPE must be set to s3 before configuring upload CORS.');

            return self::FAILURE;
        }

        $bucket = (string) config('filesystems.disks.s3.bucket');

        if ($bucket === '') {
            $this->error('AWS_BUCKET is not configured.');

            return self::FAILURE;
        }

        $origins = $files->uploadCorsOrigins(null);
        $rule = [
            'AllowedHeaders' => ['*'],
            'AllowedMethods' => ['GET', 'PUT', 'POST', 'HEAD'],
            'AllowedOrigins' => $origins,
            'ExposeHeaders' => ['ETag', 'x-amz-request-id'],
            'MaxAgeSeconds' => 3600,
        ];

        $this->info("Bucket: {$bucket}");
        $this->line('Origins:');
        foreach ($origins as $origin) {
            $this->line("  - {$origin}");
        }

        $this->newLine();
        $this->line(json_encode([$rule], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        if ($this->option('dry-run')) {
            $this->warn('Dry run only. No changes were sent to AWS.');

            return self::SUCCESS;
        }

        if (! $this->confirm('This replaces all existing CORS rules on the bucket. Continue?', true)) {
            $this->warn('Cancelled.');

            return self::SUCCESS;
        }

        try {
            $files->applyUploadCors($rule);
        } catch (RuntimeException|AwsException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('S3 upload CORS configured successfully.');
        $this->line('Try uploading a video again from the admin panel.');

        return self::SUCCESS;
    }
}
