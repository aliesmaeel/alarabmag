<?php

namespace App\Filament\Resources\InterviewResource\Pages;

use App\Filament\Resources\InterviewResource;
use App\Filament\Resources\InterviewResource\Pages\Concerns\HandlesInterviewVideo;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInterview extends EditRecord
{
    use HandlesInterviewVideo;

    protected static string $resource = InterviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
