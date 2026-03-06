<?php

namespace App\Filament\Resources\PakanResource\Widgets;

use App\Models\Pakan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PakanStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [

            Stat::make('Total Pakan', Pakan::count())
                ->description('Semua jenis pakan')
                ->icon('heroicon-o-circle-stack')
                ->color('primary'),

            Stat::make('Stok Aman', Pakan::where('stok', '>', 10)->count())
                ->description('Stok masih cukup')
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Stok Menipis', Pakan::whereBetween('stok', [0.1, 10])->count())
                ->description('Perlu segera restock')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('warning'),

            Stat::make('Stok Habis', Pakan::where('stok', '<=', 0)->count())
                ->description('Harus segera diisi')
                ->icon('heroicon-o-x-circle')
                ->color('danger'),
        ];
    }
}