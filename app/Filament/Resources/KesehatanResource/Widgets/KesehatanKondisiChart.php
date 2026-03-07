<?php

namespace App\Filament\Resources\KesehatanResource\Widgets;

use App\Models\Kesehatan;
use Filament\Widgets\ChartWidget;

class KesehatanKondisiChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Kondisi Ternak';
    
    // Tambahkan chartId untuk unique
    protected static string $chartId = 'kesehatan-kondisi-chart';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Ternak',
                    'data' => [
                        Kesehatan::sehat()->count(),
                        Kesehatan::sakit()->count(),
                        Kesehatan::kritis()->count(),
                    ],
                    'backgroundColor' => ['#4CAF50', '#FFA500', '#FF4444'], // Hijau, Oranye, Merah
                    'borderColor' => ['#45a049', '#FF8C00', '#CC0000'],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => ['Sehat', 'Sakit', 'Kritis'],
        ];
    }
    
    protected function getHeight(): int
    {
        return 300;
    }
}