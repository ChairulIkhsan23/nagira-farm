<?php

namespace App\Filament\Resources\PakanTernakResource\Pages;

use App\Filament\Resources\PakanTernakResource;
use App\Filament\Resources\PakanTernakResource\Widgets\PakanTernakStats;
use App\Filament\Resources\PakanTernakResource\Widgets\PakanDistribusiChart;
use App\Filament\Resources\PakanTernakResource\Widgets\PakanTernakTrendChart;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPakanTernaks extends ListRecords
{
    protected static string $resource = PakanTernakResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Tambah Pakan Ternak Baru')
            ->icon('heroicon-o-plus'),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            PakanTernakStats::class,
            PakanDistribusiChart::class,
            PakanTernakTrendChart::class,
        ];
    }
}
