<?php

namespace App\Filament\Resources\FatteningResource\Pages;

use App\Filament\Resources\FatteningResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFattening extends ViewRecord
{
    protected static string $resource = FatteningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}