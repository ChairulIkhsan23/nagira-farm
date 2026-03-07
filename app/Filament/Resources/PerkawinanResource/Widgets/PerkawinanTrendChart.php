<?php

namespace App\Filament\Resources\PerkawinanResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Perkawinan;
use Carbon\Carbon;

class PerkawinanTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Trend Perkawinan 6 Bulan Terakhir';
    
    // Tambahkan chartId untuk unique
    protected static string $chartId = 'perkawinan-trend-chart';

    protected function getData(): array
    {
        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M Y');
            
            $count = Perkawinan::whereYear('tanggal_kawin', $date->year)
                ->whereMonth('tanggal_kawin', $date->month)
                ->count();
            
            $data[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Perkawinan',
                    'data' => $data,
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#2196F3',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
    
    protected function getHeight(): int
    {
        return 300;
    }
}