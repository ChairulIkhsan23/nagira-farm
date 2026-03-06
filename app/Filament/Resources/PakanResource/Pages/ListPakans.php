<?php

namespace App\Filament\Resources\PakanResource\Pages;

use App\Filament\Resources\PakanResource;
use App\Filament\Resources\PakanResource\Widgets\PakanJenisChart;
use App\Filament\Resources\PakanResource\Widgets\PakanStats;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPakans extends ListRecords
{
    protected static string $resource = PakanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Tambah Pakan Baru')
            ->icon('heroicon-o-plus'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PakanStats::class,
            PakanJenisChart::class,
        ];
    }
}
