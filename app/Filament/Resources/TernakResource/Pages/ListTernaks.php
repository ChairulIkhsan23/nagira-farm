<?php

namespace App\Filament\Resources\TernakResource\Pages;

use App\Filament\Resources\TernakResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTernaks extends ListRecords
{
    protected static string $resource = TernakResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Ternak Baru')
                ->icon('heroicon-o-plus'),
        ];
    }
}
