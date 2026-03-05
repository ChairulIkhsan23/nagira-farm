<?php

namespace App\Filament\Resources\PerkawinanResource\Pages;

use App\Filament\Resources\PerkawinanResource\Widgets\PerkawinanStats;
use App\Filament\Resources\PerkawinanResource\Widgets\StatusSiklusChart;
use App\Filament\Resources\PerkawinanResource\Widgets\PerkawinanTrendChart;
use App\Filament\Resources\PerkawinanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPerkawinans extends ListRecords
{
    protected static string $resource = PerkawinanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Perkawinan Baru')
                ->icon('heroicon-o-plus'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PerkawinanStats::class,
            StatusSiklusChart::class,
            PerkawinanTrendChart::class,
        ];
    }
}