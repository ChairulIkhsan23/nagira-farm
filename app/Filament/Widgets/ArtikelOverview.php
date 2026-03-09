<?php

namespace App\Filament\Widgets;

use App\Models\Artikel;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ArtikelOverview extends StatsOverviewWidget
{
    protected int|string|array $columnSpan = 'full';
    
    protected function getStats(): array
    {
        return [

            Stat::make('Total Artikel', Artikel::count())
                ->icon('heroicon-o-document-text')
                ->color('primary'),

            Stat::make('Artikel Publish', Artikel::where('status', 'publish')->count())
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Draft Artikel', Artikel::where('status', 'draft')->count())
                ->icon('heroicon-o-pencil')
                ->color('warning'),

            Stat::make(
                'Artikel Bulan Ini',
                Artikel::whereMonth('created_at', now()->month)->count()
            )
                ->icon('heroicon-o-calendar')
                ->color('info'),

        ];
    }
}