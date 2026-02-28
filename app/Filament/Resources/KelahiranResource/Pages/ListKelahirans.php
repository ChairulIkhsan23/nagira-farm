<?php

namespace App\Filament\Resources\KelahiranResource\Pages;

use App\Filament\Resources\KelahiranResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKelahirans extends ListRecords
{
    protected static string $resource = KelahiranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Kelahiran Baru')
                ->icon('heroicon-o-plus'),
        ];
    }
}
