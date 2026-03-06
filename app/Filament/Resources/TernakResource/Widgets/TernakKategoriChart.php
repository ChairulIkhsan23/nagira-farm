<?php

namespace App\Filament\Resources\TernakResource\Widgets;

use App\Models\Ternak;
use Filament\Widgets\ChartWidget;

class TernakKategoriChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Kategori Ternak';

    protected static ?string $maxHeight = '350px';

    protected function getData(): array
    {
        $regular = Ternak::where('kategori', 'regular')->count();
        $breeding = Ternak::where('kategori', 'breeding')->count();
        $fattening = Ternak::where('kategori', 'fattening')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah',
                    'data' => [
                        $regular,
                        $breeding,
                        $fattening,
                    ],
                    'backgroundColor' => [
                        '#3b82f6', // biru - regular
                        '#22c55e', // hijau - breeding
                        '#f59e0b', // kuning - fattening
                    ],
                ],
            ],
            'labels' => [
                'Regular',
                'Breeding',
                'Fattening',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
        ];
    }
}