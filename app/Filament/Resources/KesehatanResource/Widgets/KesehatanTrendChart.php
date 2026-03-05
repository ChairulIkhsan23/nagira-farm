<?php

namespace App\Filament\Resources\KesehatanResource\Widgets;

use App\Models\Kesehatan;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class KesehatanTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Trend Pemeriksaan 6 Bulan Terakhir';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);

            $count = Kesehatan::whereMonth('tanggal_periksa', $date->month)
                ->whereYear('tanggal_periksa', $date->year)
                ->count();

            $data[] = $count;
            $labels[] = $date->format('M Y');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pemeriksaan',
                    'data' => $data,
                ],
            ],
            'labels' => $labels,
        ];
    }
}