<?php

namespace App\Filament\Resources\RiwayatTimbangResource\Pages;

use App\Filament\Resources\RiwayatTimbangResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRiwayatTimbang extends EditRecord
{
    protected static string $resource = RiwayatTimbangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
