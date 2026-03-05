<?php

namespace App\Filament\Resources\PerkawinanResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Perkawinan;
use Illuminate\Support\Facades\DB;

class PerkawinanTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Perkawinan Tahun Ini';

    protected function getData(): array
    {
        $data = Perkawinan::select(
                DB::raw('MONTH(tanggal_kawin) as bulan'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('tanggal_kawin', now()->year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');

        $labels = [];
        $totals = [];

        for ($i = 1; $i <= 12; $i++) {
            $labels[] = now()->startOfYear()->addMonths($i - 1)->format('M');
            $totals[] = $data[$i] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Perkawinan',
                    'data' => $totals,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}