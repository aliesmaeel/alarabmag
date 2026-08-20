<?php

namespace App\Filament\Resources\SongPollResource\Pages;

use App\Filament\Resources\SongPollResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSongPolls extends ListRecords
{
    protected static string $resource = SongPollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
