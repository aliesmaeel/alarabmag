<?php

namespace App\Filament\Resources\InfluencerResource\Pages;

use App\Filament\Resources\InfluencerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInfluencer extends CreateRecord
{
    protected static string $resource = InfluencerResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['category'] = 'influencer';
        return $data;
    }
}
