<?php

namespace App\Filament\Resources\KelahiranResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Kelahiran;

class KelahiranStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Statistik Kelahiran';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Jumlah',
                    'data' => [
                        Kelahiran::sum('jumlah_anak_hidup'),
                        Kelahiran::sum('jumlah_anak_mati'),
                    ],
                ],
            ],
            'labels' => [
                'Anak Hidup',
                'Anak Mati',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}