<?php

namespace App\Filament\Resources\RiwayatTimbangResource\Pages;

use App\Filament\Resources\RiwayatTimbangResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRiwayatTimbangs extends ListRecords
{
    protected static string $resource = RiwayatTimbangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
