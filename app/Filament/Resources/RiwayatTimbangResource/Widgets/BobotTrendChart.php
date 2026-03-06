<?php

namespace App\Filament\Resources\RiwayatTimbangResource\Widgets;

use App\Models\RiwayatTimbang;
use Filament\Widgets\ChartWidget;

class BobotTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Trend Penimbangan Bobot';

    protected function getData(): array
    {
        $data = RiwayatTimbang::latest('tanggal_timbang')
            ->limit(10)
            ->get()
            ->reverse()
            ->values();

        return [
            'datasets' => [
                [
                    'label' => 'Bobot (kg)',
                    'data' => $data->pluck('bobot')->toArray(),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => '#93c5fd',
                    'borderWidth' => 2,
                    'tension' => 0.3,
                    'fill' => false,
                ],
            ],
            'labels' => $data->map(fn ($item) =>
                $item->tanggal_timbang->format('d M')
            )->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}