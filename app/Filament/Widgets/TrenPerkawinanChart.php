<?php

namespace App\Filament\Widgets;

use App\Models\Perkawinan;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TrenPerkawinanChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Perkawinan & Kebuntingan';
    
    // DIPERBESAR jadi 400px atau 500px
    protected static ?string $maxHeight = '600px';

    protected function getData(): array
    {
        $tahun = date('Y');
        
        $kawinData = Perkawinan::selectRaw('MONTH(tanggal_kawin) as bulan, COUNT(*) as total')
            ->whereYear('tanggal_kawin', $tahun)
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        $buntingData = Perkawinan::bunting()
            ->selectRaw('MONTH(tanggal_kawin) as bulan, COUNT(*) as total')
            ->whereYear('tanggal_kawin', $tahun)
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        $melahirkanData = Perkawinan::melahirkan()
            ->selectRaw('MONTH(tanggal_kawin) as bulan, COUNT(*) as total')
            ->whereYear('tanggal_kawin', $tahun)
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        $labels = [
            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
        ];

        $kawinValues = [];
        $buntingValues = [];
        $melahirkanValues = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $kawinValues[] = $kawinData[$i] ?? 0;
            $buntingValues[] = $buntingData[$i] ?? 0;
            $melahirkanValues[] = $melahirkanData[$i] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Perkawinan',
                    'data' => $kawinValues,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => '#f59e0b33',
                    'tension' => 0.3,
                    'fill' => true,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                    'borderWidth' => 3,
                ],
                [
                    'label' => 'Bunting',
                    'data' => $buntingValues,
                    'borderColor' => '#22c55e',
                    'backgroundColor' => '#22c55e33',
                    'tension' => 0.3,
                    'fill' => true,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                    'borderWidth' => 3,
                ],
                [
                    'label' => 'Melahirkan',
                    'data' => $melahirkanValues,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => '#3b82f633',
                    'tension' => 0.3,
                    'fill' => true,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                    'borderWidth' => 3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'responsive' => true,
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                    'labels' => [
                        'boxWidth' => 15,
                        'padding' => 15,
                        'font' => [
                            'size' => 12,
                            'weight' => 'bold',
                        ],
                    ],
                ],
                'tooltip' => [
                    'enabled' => true,
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Jumlah (Ekor)',
                        'font' => [
                            'size' => 12,
                            'weight' => 'bold',
                        ],
                    ],
                    'grid' => [
                        'display' => true,
                        'color' => '#e5e7eb',
                    ],
                    'ticks' => [
                        'stepSize' => 1,
                        'font' => [
                            'size' => 11,
                        ],
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'font' => [
                            'size' => 11,
                            'weight' => '500',
                        ],
                    ],
                ],
            ],
        ];
    }
}