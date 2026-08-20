<?php

namespace App\Filament\Support;

use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Str;

class AudioUpload
{
    public static function make(string $name, string $label): FileUpload
    {
        return FileUpload::make($name)
            ->label(__($label))
            ->acceptedFileTypes([
                'audio/mpeg',
                'audio/mp3',
                'audio/mpeg3',
                'audio/x-mpeg-3',
                '.mp3',
            ])
            ->disk('uploads')
            ->directory('songs')
            ->visibility('public')
            ->maxSize(25600)
            ->downloadable()
            ->openable()
            ->fetchFileInformation(false)
            ->saveUploadedFileUsing(function ($file) {
                $ext = strtolower($file->getClientOriginalExtension() ?: 'mp3');
                $slug = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $name = time().'-'.($slug !== '' ? $slug : Str::random(8)).'.'.$ext;
                $file->storeAs('songs', $name, 'uploads');

                return '/uploads/songs/'.$name;
            })
            ->getUploadedFileUsing(function (?string $file): array {
                if (! filled($file)) {
                    return [];
                }

                return [
                    'name' => basename($file),
                    'size' => 0,
                    'type' => 'audio/mpeg',
                    'url' => str_starts_with($file, 'http') ? $file : url(ltrim($file, '/')),
                ];
            });
    }
}