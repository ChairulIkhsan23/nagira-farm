<?php

namespace App\Filament\Resources\KelahiranResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Kelahiran;
use Carbon\Carbon;

class KelahiranTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Trend Kelahiran 6 Bulan Terakhir';

    protected function getData(): array
    {
        $months = collect(range(0, 5))->map(function ($i) {
            return Carbon::now()->subMonths($i)->format('M Y');
        })->reverse();

        $data = $months->map(function ($month) {
            return Kelahiran::whereMonth('tanggal_melahirkan', Carbon::parse($month)->month)
                ->whereYear('tanggal_melahirkan', Carbon::parse($month)->year)
                ->count();
        });

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Kelahiran',
                    'data' => $data,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}