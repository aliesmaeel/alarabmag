<?php

namespace App\Filament\Concerns;

trait TranslatesPageLabels
{
    public static function getNavigationLabel(): string
    {
        return __(static::$navigationLabel ?? parent::getNavigationLabel());
    }

    public static function getNavigationGroup(): ?string
    {
        $group = static::$navigationGroup ?? null;

        return filled($group) ? __($group) : null;
    }

    public function getTitle(): string
    {
        return __(static::$title ?? parent::getTitle());
    }
}
