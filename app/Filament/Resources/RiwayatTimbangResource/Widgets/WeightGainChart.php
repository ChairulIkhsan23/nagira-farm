<?php

namespace App\Filament\Resources\RiwayatTimbangResource\Widgets;

use App\Models\RiwayatTimbang;
use Filament\Widgets\ChartWidget;

class WeightGainChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Perubahan Bobot';

    protected static ?string $maxHeight = '350px';

    protected function getData(): array
    {
        $records = RiwayatTimbang::all();

        $naik = 0;
        $turun = 0;
        $stabil = 0;

        foreach ($records as $record) {
            $diff = $record->selisih_bobot;

            if ($diff > 0) {
                $naik++;
            } elseif ($diff < 0) {
                $turun++;
            } else {
                $stabil++;
            }
        }

        return [
            'datasets' => [
                [
                    'data' => [$naik, $turun, $stabil],
                    'backgroundColor' => [
                        '#22c55e',
                        '#ef4444',
                        '#facc15',
                    ],
                ],
            ],
            'labels' => [
                'Naik',
                'Turun',
                'Stabil',
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