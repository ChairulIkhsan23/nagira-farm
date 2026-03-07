<?php

namespace App\Filament\Resources\PakanTernakResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\PakanTernak;
use App\Models\Pakan;
use Illuminate\Support\Facades\DB;

class PakanDistribusiChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Pemberian Pakan';
    
    protected static string $chartId = 'pakan-distribusi-chart';

    protected function getData(): array
    {
        // Top 5 pakan paling sering digunakan
        $topPakan = PakanTernak::select('pakan_id', DB::raw('COUNT(*) as total'))
            ->with('pakan')
            ->groupBy('pakan_id')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        $labels = [];
        $data = [];
        $colors = ['#4CAF50', '#2196F3', '#FF9800', '#9C27B0', '#F44336'];

        foreach ($topPakan as $index => $item) {
            $labels[] = $item->pakan?->nama_pakan ?? 'Pakan #' . $item->pakan_id;
            $data[] = $item->total;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pemberian',
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                    'borderColor' => array_slice($colors, 0, count($data)),
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
    
    protected function getHeight(): int
    {
        return 300;
    }
}