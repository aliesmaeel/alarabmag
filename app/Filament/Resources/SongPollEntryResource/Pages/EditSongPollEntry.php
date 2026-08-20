<?php

namespace App\Filament\Resources\SongPollEntryResource\Pages;

use App\Filament\Resources\SongPollEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSongPollEntry extends EditRecord
{
    protected static string $resource = SongPollEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
