<?php

namespace App\Filament\Widgets;

use App\Models\Artikel;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Ternak;
use App\Models\Pakan;
use App\Models\PakanTernak;

class GeneralOverview extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';
    
    protected function getStats(): array
    {
        return [
            Stat::make('Total Kambing', Ternak::count())
                ->icon('heroicon-o-clipboard-document-list')
                ->color('success'),

            Stat::make('Jenis Kambing', Ternak::select('jenis_ternak')->distinct()->count())
                ->icon('heroicon-o-tag')
                ->color('primary'),

            Stat::make('Total Artikel', Artikel::count())
                ->icon('heroicon-o-document-text')
                ->color('primary'),

            Stat::make('Artikel Publish', Artikel::where('status', 'publish')->count())
                ->icon('heroicon-o-check-circle')
                ->color('success'),
        ];
    }
}
