<?php

namespace App\Filament\Support;

use App\Services\FileUploadService;
use Filament\Forms\Components\FileUpload;

class HtmlUpload
{
    public static function make(string $name, string $label): FileUpload
    {
        return FileUpload::make($name)
            ->label($label)
            ->acceptedFileTypes(['text/html', 'application/xhtml+xml', '.html', '.htm'])
            ->maxSize(10240)
            ->required()
            ->fetchFileInformation(false)
            ->saveUploadedFileUsing(function ($file): string {
                return app(FileUploadService::class)->uploadFile($file, 'magazine/issues');
            })
            ->getUploadedFileUsing(function (?string $file): array {
                if (! filled($file)) {
                    return [];
                }

                $url = app(FileUploadService::class)->resolveUrl($file);

                return [
                    'name' => basename($file),
                    'size' => 0,
                    'type' => 'text/html',
                    'url' => $url ?? $file,
                ];
            })
            ->helperText('ملف HTML واحد فقط. يجب أن تكون الصور والتنسيقات مرتبطة داخل الملف (روابط خارجية أو inline).')
            ->columnSpanFull();
    }
}
