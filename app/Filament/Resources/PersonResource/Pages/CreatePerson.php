<?php

namespace App\Filament\Resources\PersonResource\Pages;

use App\Filament\Resources\PersonResource;
use App\Filament\Support\ValidatesPersonBio;
use Filament\Resources\Pages\CreateRecord;

class CreatePerson extends CreateRecord
{
    use ValidatesPersonBio;

    protected static string $resource = PersonResource::class;
}
