<?php

namespace App\Filament\Resources\KesehatanResource\Widgets;

use App\Models\Kesehatan;
use Filament\Widgets\ChartWidget;

class KesehatanKondisiChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Kondisi Ternak';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Jumlah',
                    'data' => [
                        Kesehatan::sehat()->count(),
                        Kesehatan::sakit()->count(),
                        Kesehatan::kritis()->count(),
                    ],
                ],
            ],
            'labels' => ['Sehat', 'Sakit', 'Kritis'],
        ];
    }
}