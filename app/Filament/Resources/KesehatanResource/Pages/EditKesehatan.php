<?php

namespace App\Filament\Resources\KesehatanResource\Pages;

use App\Filament\Resources\KesehatanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKesehatan extends EditRecord
{
    protected static string $resource = KesehatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
