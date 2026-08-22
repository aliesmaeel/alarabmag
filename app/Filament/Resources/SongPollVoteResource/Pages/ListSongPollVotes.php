<?php

namespace App\Filament\Resources\SongPollVoteResource\Pages;

use App\Filament\Resources\SongPollVoteResource;
use Filament\Resources\Pages\ListRecords;

class ListSongPollVotes extends ListRecords
{
    protected static string $resource = SongPollVoteResource::class;

    public function getSubheading(): ?string
    {
        $count = $this->getFilteredTableQuery()?->count()
            ?? static::getResource()::getEloquentQuery()->count();

        return __('dashboard.records_count', ['count' => number_format($count)]);
    }
}
