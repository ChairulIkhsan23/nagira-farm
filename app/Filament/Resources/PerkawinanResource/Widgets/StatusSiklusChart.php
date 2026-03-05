<?php

namespace App\Filament\Resources\PerkawinanResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Perkawinan;

class StatusSiklusChart extends ChartWidget
{
    protected static ?string $heading = 'Statistik Perkawinan';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Jumlah',
                    'data' => [
                        Perkawinan::kawin()->count(),
                        Perkawinan::bunting()->count(),
                        Perkawinan::gagal()->count(),
                        Perkawinan::melahirkan()->count(),
                    ],
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
}