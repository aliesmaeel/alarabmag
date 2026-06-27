<?php

namespace App\Filament\Support;

use App\Services\FileUploadService;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Support\Str;

class ImageUpload
{
    public static function resolveUrl(?string $value): ?string
    {
        return app(FileUploadService::class)->resolveUrl($value);
    }

    public static function column(string $name = 'image_url', string $label = 'الصورة'): ImageColumn
    {
        return ImageColumn::make($name)
            ->label($label)
            ->circular()
            ->getStateUsing(fn ($record) => self::resolveUrl($record->{$name} ?? null));
    }

    /**
     * @param  string|null  $aspectRatio  When set (e.g. "16:9"), the uploaded image is cropped to
     *                                     this ratio on upload so it displays consistently on the site.
     */
    public static function make(string $name, string $label, ?string $aspectRatio = null): FileUpload
    {
        $field = FileUpload::make($name)
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

        if ($aspectRatio !== null) {
            $field
                ->imageCropAspectRatio($aspectRatio)
                ->imageEditorAspectRatios([$aspectRatio, null])
                ->imageEditorMode(2);
        }

        return $field;
    }
}
