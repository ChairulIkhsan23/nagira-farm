<?php

namespace App\Filament\Resources\PakanTernakResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\PakanTernak;
use Carbon\Carbon;

class PakanTernakTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Trend Pemberian Pakan 6 Bulan Terakhir';
    
    protected static string $chartId = 'pakan-ternak-trend-chart';

    protected function getData(): array
    {
        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M Y');
            
            $count = PakanTernak::whereYear('tanggal', $date->year)
                ->whereMonth('tanggal', $date->month)
                ->count();
            
            $data[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pemberian Pakan',
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