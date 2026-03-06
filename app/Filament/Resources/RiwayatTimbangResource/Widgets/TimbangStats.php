<?php

namespace App\Filament\Resources\RiwayatTimbangResource\Widgets;

use App\Models\RiwayatTimbang;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TimbangStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Timbang Hari Ini', RiwayatTimbang::hariIni()->count())
                ->color('success')
                ->icon('heroicon-o-scale'),

            Stat::make('Timbang Minggu Ini', RiwayatTimbang::mingguIni()->count())
                ->color('primary')
                ->icon('heroicon-o-calendar'),

            Stat::make('Timbang Bulan Ini', RiwayatTimbang::bulanIni()->count())
                ->color('warning')
                ->icon('heroicon-o-clock'),
        ];
    }
}