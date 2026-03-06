<?php

namespace App\Filament\Resources\TernakResource\Widgets;

use App\Models\Ternak;
use App\Enums\JenisTernak;
use Filament\Widgets\ChartWidget;

class TernakJenisChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Jenis Ternak';

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $jenisList = collect(JenisTernak::cases())->pluck('value');

        $dbData = Ternak::selectRaw('jenis_ternak, count(*) as total')
            ->groupBy('jenis_ternak')
            ->pluck('total', 'jenis_ternak');

        $data = $jenisList->map(fn ($jenis) => $dbData[$jenis] ?? 0);

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Ternak',
                    'data' => $data->values(),
                    'backgroundColor' => '#3b82f6',
                    'borderColor' => '#ffffff',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $jenisList->values(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }

}