<?php

namespace App\Filament\Support;

use Filament\Forms\Components\RichEditor;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class RichEditorField
{
    public static function make(string $name): RichEditor
    {
        return RichEditor::make($name)
            ->fileAttachmentsDisk('uploads')
            ->fileAttachmentsDirectory('editor')
            ->fileAttachmentsVisibility('public')
            ->saveUploadedFileAttachmentsUsing(function (TemporaryUploadedFile $file): string {
                $ext = $file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'bin';
                $slug = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $basename = time().'-'.($slug !== '' ? $slug : Str::random(8)).'.'.$ext;
                $file->storeAs('editor', $basename, 'uploads');

                return 'editor/'.$basename;
            })
            ->getUploadedAttachmentUrlUsing(function (string $file): string {
                return '/uploads/'.ltrim($file, '/');
            });
    }
}
