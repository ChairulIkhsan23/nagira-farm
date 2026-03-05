<?php

namespace App\Filament\Resources\PerkawinanResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Perkawinan;

class PerkawinanStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [

            Stat::make('Total Perkawinan', Perkawinan::count())
                ->description('Semua data')
                ->color('primary'),

            Stat::make('Bunting', Perkawinan::bunting()->count())
                ->description('Sedang hamil')
                ->color('success'),

            Stat::make('Gagal', Perkawinan::gagal()->count())
                ->color('danger'),

            Stat::make('Perkiraan Lahir (7 Hari kedepan)', Perkawinan::nearDue()->count())
                ->color('warning'),

        ];
    }
}