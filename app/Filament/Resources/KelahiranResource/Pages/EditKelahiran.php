<?php

namespace App\Filament\Resources\KelahiranResource\Pages;

use App\Filament\Resources\KelahiranResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKelahiran extends EditRecord
{
    protected static string $resource = KelahiranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
