<?php

namespace App\Filament\Resources\RiwayatTimbangResource\Pages;

use App\Filament\Resources\RiwayatTimbangResource;
use App\Filament\Resources\RiwayatTimbangResource\Widgets\BobotTrendChart;
use App\Filament\Resources\RiwayatTimbangResource\Widgets\TimbangStats;
use App\Filament\Resources\RiwayatTimbangResource\Widgets\WeightGainChart;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRiwayatTimbangs extends ListRecords
{
    protected static string $resource = RiwayatTimbangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Riwayat Timbang Baru')
                ->icon('heroicon-o-plus'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TimbangStats::class,
            WeightGainChart::class,
            BobotTrendChart::class,
        ];
    }
}
