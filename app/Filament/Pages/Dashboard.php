<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\HasPagePermission;

class Dashboard extends \Filament\Pages\Dashboard
{
    use HasPagePermission;

    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $permissionKey = 'access_dashboard';
}
