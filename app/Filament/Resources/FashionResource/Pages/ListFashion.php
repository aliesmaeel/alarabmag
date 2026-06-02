<?php

namespace App\Filament\Resources\FashionResource\Pages;

use App\Filament\Resources\FashionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFashion extends ListRecords
{
    protected static string $resource = FashionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
