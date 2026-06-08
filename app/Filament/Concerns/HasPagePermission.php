<?php

namespace App\Filament\Concerns;

trait HasPagePermission
{
    protected static function isAuthorized(): bool
    {
        return auth()->user()?->hasPermissionTo(static::$permissionKey) ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::isAuthorized();
    }

    public static function canAccess(): bool
    {
        return static::isAuthorized();
    }
}
