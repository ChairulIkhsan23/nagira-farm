<?php

namespace App\Filament\Resources\FatteningResource\Widgets;

use App\Models\Fattening;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class FatteningMonthlyChart extends ChartWidget
{
    protected static ?string $heading = 'Program Fattening per Bulan';

    protected function getData(): array
    {
        $data = Fattening::selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        $monthlyData = [];

        for ($i = 1; $i <= 12; $i++) {
            $monthlyData[] = $data[$i] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Program',
                    'data' => $monthlyData,
                    'borderWidth' => 2,
                    'tension' => 0.3,
                    'fill' => false,
                ],
            ],
            'labels' => [
                'Jan','Feb','Mar','Apr','Mei','Jun',
                'Jul','Agu','Sep','Okt','Nov','Des'
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}