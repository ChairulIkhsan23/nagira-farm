<?php

namespace App\Filament\Resources\TernakResource\Pages;

use App\Filament\Resources\TernakResource;
use App\Filament\Resources\TernakResource\Widgets\TernakChartWithHighlight;
use App\Filament\Resources\TernakResource\Widgets\TernakJenisChart;
use App\Filament\Resources\TernakResource\Widgets\TernakKategoriChart;
use App\Filament\Resources\TernakResource\Widgets\TernakStats;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\TernakResource\Widgets\TernakStatsOverview;

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

    protected function getHeaderWidgets(): array
    {
        return [
            TernakStats::class,
            TernakJenisChart::class,
            TernakKategoriChart::class,
        ];
    }
}
