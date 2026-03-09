<?php

namespace App\Filament\Widgets;

use App\Models\Fattening;
use Filament\Widgets\ChartWidget;

class StatusFatteningChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Status Fattening';
    protected static ?string $maxHeight = '150px';

    protected function getData(): array
    {
        $aktif = Fattening::aktif()->count();
        $selesai = Fattening::selesai()->count();
        $gagal = Fattening::gagal()->count();
        $overdue = Fattening::overdue()->count();

        return [
            'datasets' => [
                [
                    'data' => [$aktif - $overdue, $overdue, $selesai, $gagal],
                    'backgroundColor' => [
                        '#3b82f6', // biru - aktif tepat waktu
                        '#f59e0b', // kuning - overdue
                        '#22c55e', // hijau - selesai
                        '#ef4444', // merah - gagal
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => [
                'Aktif Tepat Waktu',
                'Terlambat (Overdue)',
                'Selesai',
                'Gagal',
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
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'boxWidth' => 12,
                        'padding' => 10,
                    ],
                ],
            ],
        ];
    }
}