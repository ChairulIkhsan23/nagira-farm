<?php

namespace App\Filament\Resources\PakanTernakResource\Pages;

use App\Filament\Resources\PakanTernakResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPakanTernaks extends ListRecords
{
    protected static string $resource = PakanTernakResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Tambah Ternak Baru')
            ->icon('heroicon-o-plus'),
        ];
    }
}
