<?php

namespace App\Filament\Resources\PerkawinanResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Perkawinan;

class StatusSiklusChart extends ChartWidget
{
    protected static ?string $heading = 'Statistik Perkawinan';
    
    // Tambahkan chartId seperti di kelahiran
    protected static string $chartId = 'status-siklus-chart';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Status Perkawinan',
                    'data' => [
                        Perkawinan::kawin()->count(),
                        Perkawinan::bunting()->count(),
                        Perkawinan::gagal()->count(),
                        Perkawinan::melahirkan()->count(),
                    ],
                    'backgroundColor' => ['#FFA500', '#4CAF50', '#FF4444', '#2196F3'],
                    'borderColor' => ['#FF8C00', '#45a049', '#CC0000', '#1976D2'],
                ],
            ],
            'labels' => [
                'Kawin',
                'Bunting',
                'Gagal',
                'Melahirkan',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
    
    protected function getHeight(): int
    {
        return 300;
    }
}