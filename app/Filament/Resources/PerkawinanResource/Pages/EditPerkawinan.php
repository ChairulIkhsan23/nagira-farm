<?php

namespace App\Filament\Resources\PerkawinanResource\Pages;

use App\Filament\Resources\PerkawinanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPerkawinan extends EditRecord
{
    protected static string $resource = PerkawinanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
