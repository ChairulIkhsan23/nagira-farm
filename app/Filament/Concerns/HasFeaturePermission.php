<?php

namespace App\Filament\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasFeaturePermission
{
    protected static function getPermissionKey(): string
    {
        if (property_exists(static::class, 'permissionKey') && filled(static::$permissionKey)) {
            return static::$permissionKey;
        }

        return Str::snake(Str::before(class_basename(static::class), 'Resource'));
    }

    protected static function usesActionPermissions(): bool
    {
        return ! Str::startsWith(static::getPermissionKey(), [
            'access_',
            'view_',
            'create_',
            'update_',
            'delete_',
        ]);
    }

    protected static function getPermissionSlug(?string $action = null): string
    {
        $permissionKey = static::getPermissionKey();

        if (! static::usesActionPermissions()) {
            return $permissionKey;
        }

        return sprintf('%s_%s', $action ?? 'view', $permissionKey);
    }

    protected static function hasPermission(string $permission): bool
    {
        return auth()->user()?->hasPermissionTo($permission) ?? false;
    }

    protected static function isAuthorized(?string $action = null): bool
    {
        if (! static::usesActionPermissions()) {
            return static::hasPermission(static::getPermissionSlug());
        }

        if ($action !== null) {
            return static::hasPermission(static::getPermissionSlug($action));
        }

        return collect(['view', 'create', 'update', 'delete'])
            ->contains(fn (string $permissionAction): bool => static::hasPermission(static::getPermissionSlug($permissionAction)));
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::isAuthorized();
    }

    public static function canAccess(): bool
    {
        return static::isAuthorized();
    }

    public static function canViewAny(): bool
    {
        if (! static::usesActionPermissions()) {
            return static::isAuthorized();
        }

        return static::isAuthorized('view')
            || static::isAuthorized('update')
            || static::isAuthorized('delete');
    }

    public static function canView(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canCreate(): bool
    {
        return static::usesActionPermissions()
            ? static::isAuthorized('create')
            : static::isAuthorized();
    }

    public static function canEdit(Model $record): bool
    {
        return static::usesActionPermissions()
            ? static::isAuthorized('update')
            : static::isAuthorized();
    }

    public static function canDelete(Model $record): bool
    {
        return static::usesActionPermissions()
            ? static::isAuthorized('delete')
            : static::isAuthorized();
    }

    public static function canDeleteAny(): bool
    {
        return static::usesActionPermissions()
            ? static::isAuthorized('delete')
            : static::isAuthorized();
    }

    public static function canForceDelete(Model $record): bool
    {
        return static::canDelete($record);
    }

    public static function canForceDeleteAny(): bool
    {
        return static::canDeleteAny();
    }

    public static function canRestore(Model $record): bool
    {
        return static::usesActionPermissions()
            ? static::isAuthorized('update')
            : static::isAuthorized();
    }

    public static function canRestoreAny(): bool
    {
        return static::usesActionPermissions()
            ? static::isAuthorized('update')
            : static::isAuthorized();
    }

    public static function canReplicate(Model $record): bool
    {
        return static::usesActionPermissions()
            ? static::isAuthorized('create')
            : static::isAuthorized();
    }

    public static function canReorder(): bool
    {
        return static::usesActionPermissions()
            ? static::isAuthorized('update')
            : static::isAuthorized();
    }
}
