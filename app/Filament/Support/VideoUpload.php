<?php

namespace App\Filament\Support;

use App\Filament\Forms\Components\S3VideoUpload;

class VideoUpload
{
    public static function make(string $name = 'video_url', string $label = 'ملف الفيديو'): S3VideoUpload
    {
        return S3VideoUpload::make($name)
            ->label($label)
            ->maxSize(512000)
            ->helperText('ارفع ملف فيديو (mp4, webm, mov) — يُخزَّن على Amazon S3. حد أقصى 500 ميجابايت.');
    }
}
