<?php

namespace App\Filament\Resources\KesehatanResource\Widgets;

use App\Models\Kesehatan;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class KesehatanTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Trend Pemeriksaan 6 Bulan Terakhir';
    
    
    protected static string $chartId = 'kesehatan-trend-chart';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M Y');
            
            $count = Kesehatan::whereMonth('tanggal_periksa', $date->month)
                ->whereYear('tanggal_periksa', $date->year)
                ->count();

            $data[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pemeriksaan',
                    'data' => $data,
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#2196F3',
                    'tension' => 0.4, 
                ],
            ],
            'labels' => $labels,
        ];
    }
    
    protected function getHeight(): int
    {
        return 300;
    }
}