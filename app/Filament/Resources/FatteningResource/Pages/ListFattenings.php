<?php

namespace App\Filament\Resources\FatteningResource\Pages;

use App\Filament\Resources\FatteningResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFattenings extends ListRecords
{
    protected static string $resource = FatteningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
