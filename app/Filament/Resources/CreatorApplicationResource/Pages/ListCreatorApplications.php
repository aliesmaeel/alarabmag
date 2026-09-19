<?php

namespace App\Filament\Resources\CreatorApplicationResource\Pages;

use App\Filament\Resources\CreatorApplicationResource;
use App\Models\CreatorApplication;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListCreatorApplications extends ListRecords
{
    protected static string $resource = CreatorApplicationResource::class;

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make(__('الكل')),
        ];

        foreach (CreatorApplication::STATUSES as $value => $label) {
            $tabs[$value] = Tab::make(__($label))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', $value))
                ->badge(CreatorApplication::query()->where('status', $value)->count());
        }

        return $tabs;
    }
}
