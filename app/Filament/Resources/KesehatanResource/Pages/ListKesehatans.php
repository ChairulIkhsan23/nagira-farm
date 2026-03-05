<?php

namespace App\Filament\Resources\KesehatanResource\Pages;

use App\Filament\Resources\KesehatanResource\Widgets\KesehatanKondisiChart;
use App\Filament\Resources\KesehatanResource\Widgets\KesehatanStats;
use App\Filament\Resources\KesehatanResource\Widgets\KesehatanTrendChart;

use App\Filament\Resources\KesehatanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKesehatans extends ListRecords
{
    protected static string $resource = KesehatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Kesehatan Baru')
                ->icon('heroicon-o-plus'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            KesehatanStats::class,
            KesehatanKondisiChart::class,
            KesehatanTrendChart::class,
        ];
    }
}
