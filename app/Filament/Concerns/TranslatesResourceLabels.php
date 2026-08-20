<?php

namespace App\Filament\Concerns;

trait TranslatesResourceLabels
{
    public static function getNavigationLabel(): string
    {
        return __(static::$navigationLabel ?? parent::getNavigationLabel());
    }

    public static function getModelLabel(): string
    {
        return __(static::$modelLabel ?? parent::getModelLabel());
    }

    public static function getPluralModelLabel(): string
    {
        return __(static::$pluralModelLabel ?? parent::getPluralModelLabel());
    }

    public static function getNavigationGroup(): ?string
    {
        $group = static::$navigationGroup ?? null;

        return filled($group) ? __($group) : null;
    }
}
