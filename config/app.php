<?php

use Illuminate\Support\Facades\Facade;

return [
    'name'            => env('APP_NAME', 'مجلة العرب'),
    'env'             => env('APP_ENV', 'production'),
    'debug'           => (bool) env('APP_DEBUG', false),
    'url'             => env('APP_URL', 'http://localhost'),
    'timezone'        => 'Asia/Dubai',
    'locale'          => 'ar',
    'fallback_locale' => 'en',
    'faker_locale'    => 'ar_SA',
    'key'             => env('APP_KEY'),
    'cipher'          => 'AES-256-CBC',

    'providers' => Illuminate\Support\AggregateServiceProvider::defaultProviders()->merge([
        App\Providers\AppServiceProvider::class,
    ])->toArray(),

    'aliases' => Facade::defaultAliases()->merge([])->toArray(),
];
