<?php

namespace App\Filament\Resources\FashionResource\Pages;

use App\Filament\Resources\FashionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFashion extends EditRecord
{
    protected static string $resource = FashionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
