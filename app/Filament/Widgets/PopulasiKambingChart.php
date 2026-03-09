<?php

namespace App\Filament\Widgets;

use App\Models\Ternak;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PopulasiKambingChart extends ChartWidget
{
    protected static ?string $heading = 'Pertumbuhan Populasi Kambing';

    protected function getData(): array
    {
        $data = Ternak::selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        $labels = [
            'Jan','Feb','Mar','Apr','Mei','Jun',
            'Jul','Agu','Sep','Okt','Nov','Des'
        ];

        $values = [];

        for ($i = 1; $i <= 12; $i++) {
            $values[] = $data[$i] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Kambing',
                    'data' => $values,
                    'borderColor' => '#22c55e',
                    'backgroundColor' => '#22c55e33',
                    'tension' => 0.3,
                    'fill' => false,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
     // DIPERBESAR jadi 400px atau 500px
    protected static ?string $maxHeight = '100px';
    
    // Full width biar lebih dominan
    protected int | string | array $columnSpan = 'full';
}