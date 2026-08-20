<?php

namespace App\Filament\Resources\SongPollEntryResource\Pages;

use App\Filament\Resources\SongPollEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSongPollEntries extends ListRecords
{
    protected static string $resource = SongPollEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
