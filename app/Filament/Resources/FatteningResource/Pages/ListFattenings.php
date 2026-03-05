<?php

namespace App\Filament\Resources\FatteningResource\Pages;

use App\Filament\Resources\FatteningResource;
use App\Filament\Resources\FatteningResource\Widgets\FatteningMonthlyChart;
use App\Filament\Resources\FatteningResource\Widgets\FatteningProgressChart;
use App\Filament\Resources\FatteningResource\Widgets\FatteningStats;
use App\Filament\Resources\FatteningResource\Widgets\FatteningWeightProgress;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFattenings extends ListRecords
{
    protected static string $resource = FatteningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Fattening Baru')
                ->icon('heroicon-o-plus'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            FatteningStats::class,
            FatteningProgressChart::class,
            FatteningMonthlyChart::class,
        ];
    }
    
}
