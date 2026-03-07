<?php

namespace App\Filament\Resources\KelahiranResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Kelahiran;
use Carbon\Carbon;

class KelahiranTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Trend Kelahiran 6 Bulan Terakhir';
    
    // Tambahkan ini untuk memastikan widget unique
    protected static string $chartId = 'kelahiran-trend-chart';

    protected function getData(): array
    {
        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M Y');
            
            $count = Kelahiran::whereYear('tanggal_melahirkan', $date->year)
                ->whereMonth('tanggal_melahirkan', $date->month)
                ->count();
            
            $data[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Kelahiran',
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
    
    // Tambahkan height tetap
    protected function getHeight(): int
    {
        return 300;
    }
}