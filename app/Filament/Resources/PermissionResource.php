<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasFeaturePermission;
use App\Filament\Resources\PermissionResource\Pages;
use App\Models\Permission;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PermissionResource extends Resource
{
    use HasFeaturePermission;

    protected static ?string $model = Permission::class;
    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?string $navigationGroup = 'Manajemen Akses';
    protected static ?string $navigationLabel = 'Permission';
    protected static ?string $modelLabel = 'Permission';
    protected static ?string $pluralModelLabel = 'Daftar Permission';
    protected static ?string $permissionKey = 'permissions';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Permission')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('group')
                    ->label('Grup')
                    ->badge(),
                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->wrap(),
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermissions::route('/'),
        ];
    }
}
