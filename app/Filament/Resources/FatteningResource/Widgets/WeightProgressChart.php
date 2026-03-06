<?php

namespace App\Filament\Resources\FatteningResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\RiwayatTimbang;

class WeightProgressChart extends ChartWidget
{
    protected static ?string $heading = 'Trend Bobot Program Fattening';

    public $record;

    protected function getData(): array
    {
        $data = RiwayatTimbang::where('fattening_id', $this->record->id)
            ->orderBy('tanggal_timbang')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Bobot (kg)',
                    'data' => $data->pluck('bobot'),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => '#93c5fd',
                    'tension' => 0.4
                ],
            ],
            'labels' => $data->map(fn ($item) =>
                $item->tanggal_timbang->format('d M')
            ),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected int|string|array $columnSpan = 'full';
}