<?php

namespace App\Filament\Support;

use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Support\Str;

class ImageUpload
{
    public static function resolveUrl(?string $value): ?string
    {
        if (! $value) {
            return null;
        }
        if (preg_match('#^(https?:)?//#i', $value) || str_starts_with($value, 'data:')) {
            return $value;
        }
        return url(ltrim($value, '/'));
    }

    public static function column(string $name = 'image_url', string $label = 'الصورة'): ImageColumn
    {
        return ImageColumn::make($name)
            ->label($label)
            ->circular()
            ->getStateUsing(fn ($record) => self::resolveUrl($record->{$name} ?? null));
    }

    public static function make(string $name, string $label): FileUpload
    {
        return FileUpload::make($name)
            ->label($label)
            ->image()
            ->disk('uploads')
            ->visibility('public')
            ->maxSize(15360)
            ->imageEditor()
            ->fetchFileInformation(false)
            ->saveUploadedFileUsing(function ($file) {
                $ext  = $file->getClientOriginalExtension();
                $slug = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $name = time() . '-' . ($slug ?: Str::random(6)) . '.' . $ext;
                $file->storeAs('', $name, 'uploads');
                return '/uploads/' . $name;
            })
            ->getUploadedFileUsing(fn (string $file): array => [
                'name' => basename($file),
                'size' => 0,
                'type' => null,
                'url'  => $file,
            ]);
    }
}
