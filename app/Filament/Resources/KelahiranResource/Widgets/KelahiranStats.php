<?php

namespace App\Filament\Resources\KelahiranResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Kelahiran;

class KelahiranStats extends BaseWidget
{
    protected function getStats(): array
    {
        $total = Kelahiran::count();
        $hidup = Kelahiran::sum('jumlah_anak_hidup');
        $mati  = Kelahiran::sum('jumlah_anak_mati');

        $survivalRate = $hidup + $mati > 0
            ? round(($hidup / ($hidup + $mati)) * 100, 1)
            : 0;

        return [

            Stat::make('Total Kelahiran', $total)
                ->color('primary'),

            Stat::make('Total Anak Hidup', $hidup)
                ->color('success'),

            Stat::make('Total Anak Mati', $mati)
                ->color('danger'),

            Stat::make('Survival Rate', $survivalRate . '%')
                ->color('info'),
        ];
    }
}