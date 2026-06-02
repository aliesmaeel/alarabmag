<?php

namespace App\Filament\Resources\FashionResource\Pages;

use App\Filament\Resources\FashionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFashion extends CreateRecord
{
    protected static string $resource = FashionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['category'] = 'موضة';

        return $data;
    }
}
