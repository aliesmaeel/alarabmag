<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $appUrl = config('app.url');

        if (config('app.env') === 'production' && filled($appUrl)) {
            URL::forceRootUrl($appUrl);
            URL::forceScheme(parse_url($appUrl, PHP_URL_SCHEME) ?: 'https');
        }

        config([
            'livewire.temporary_file_upload.rules' => ['file', 'max:25600'],
            'livewire.temporary_file_upload.max_upload_time' => 10,
        ]);
    }
}
