<?php

namespace App\Filament\Resources\SongPollResource\Pages;

use App\Filament\Resources\SongPollResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSongPoll extends EditRecord
{
    protected static string $resource = SongPollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
