<?php

namespace App\Filament\Resources\TernakResource\Pages;

use App\Filament\Resources\TernakResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTernak extends EditRecord
{
    protected static string $resource = TernakResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
