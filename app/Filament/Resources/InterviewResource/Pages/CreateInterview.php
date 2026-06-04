<?php

namespace App\Filament\Resources\InterviewResource\Pages;

use App\Filament\Resources\InterviewResource;
use App\Filament\Resources\InterviewResource\Pages\Concerns\HandlesInterviewVideo;
use Filament\Resources\Pages\CreateRecord;

class CreateInterview extends CreateRecord
{
    use HandlesInterviewVideo;

    protected static string $resource = InterviewResource::class;
}
