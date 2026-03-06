<?php

namespace App\Filament\Resources\FatteningResource\Widgets;

use App\Models\Fattening;
use Filament\Widgets\ChartWidget;

class FatteningProgressChart extends ChartWidget
{
    protected static ?string $heading = 'Status Program Fattening';

    protected static ?string $maxHeight = '350px';

    protected function getData(): array
    {
        $progres = Fattening::where('status', 'progres')->count();
        $selesai = Fattening::where('status', 'selesai')->count();
        $gagal = Fattening::where('status', 'gagal')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Status',
                    'data' => [
                        $progres,
                        $selesai,
                        $gagal
                    ],
                    'backgroundColor' => [
                        '#3b82f6', // biru - progress
                        '#22c55e', // hijau - selesai
                        '#ef4444', // merah - gagal
                    ],
                    'borderColor' => [
                        '#ffffff',
                        '#ffffff',
                        '#ffffff',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => [
                'Progress',
                'Selesai',
                'Gagal'
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
        ];
    }
}