<?php

namespace App\Filament\Resources\SongPollResource\Pages;

use App\Filament\Resources\SongPollResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSongPoll extends CreateRecord
{
    protected static string $resource = SongPollResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
