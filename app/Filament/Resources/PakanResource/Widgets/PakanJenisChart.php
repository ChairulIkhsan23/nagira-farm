<?php

namespace App\Filament\Resources\PakanResource\Widgets;

use App\Models\Pakan;
use App\Enums\JenisPakan;
use Filament\Widgets\ChartWidget;

class PakanJenisChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Jenis Pakan';

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $enumJenis = collect(JenisPakan::cases())->pluck('value');

        $dbData = Pakan::selectRaw('jenis_pakan, count(*) as total')
            ->groupBy('jenis_pakan')
            ->pluck('total','jenis_pakan');

        $data = $enumJenis->map(fn ($jenis) => $dbData[$jenis] ?? 0);

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah',
                    'data' => $data->values(),
                    'backgroundColor' => [
                        '#3b82f6',
                        '#22c55e',
                        '#f59e0b',
                        '#ef4444',
                        '#8b5cf6',
                    ],
                    'borderColor' => '#ffffff',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $enumJenis->values(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}