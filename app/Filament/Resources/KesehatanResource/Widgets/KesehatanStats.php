<?php

namespace App\Filament\Resources\KesehatanResource\Widgets;

use App\Models\Kesehatan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class KesehatanStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Pemeriksaan', Kesehatan::count())
                ->description('Semua data kesehatan')
                ->color('primary')
                ->icon('heroicon-o-clipboard-document'),

            Stat::make('Sehat', Kesehatan::sehat()->count())
                ->description('Kondisi sehat')
                ->color('success')
                ->icon('heroicon-o-check-circle'),

            Stat::make('Sakit', Kesehatan::sakit()->count())
                ->description('Butuh penanganan')
                ->color('warning')
                ->icon('heroicon-o-exclamation-circle'),

            Stat::make('Kritis', Kesehatan::kritis()->count())
                ->description('Butuh tindakan segera')
                ->color('danger')
                ->icon('heroicon-o-x-circle'),
        ];
    }
}