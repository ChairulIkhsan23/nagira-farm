<?php

namespace App\Filament\Resources\KelahiranResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Kelahiran;

class KelahiranStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Status Kelahiran';
    
    
    protected static string $chartId = 'kelahiran-status-chart';

    protected function getData(): array
    {
        
        $totalLahir = Kelahiran::sum('jumlah_anak_lahir') ?: 1;
        $totalHidup = Kelahiran::sum('jumlah_anak_hidup');
        $totalMati = Kelahiran::sum('jumlah_anak_mati');
        
        
        $menyusui = Kelahiran::whereDate('tanggal_sapih', '>', now())->count();
        $sudahSapih = Kelahiran::whereDate('tanggal_sapih', '<=', now())->count();

        return [
            'datasets' => [
                [
                    'label' => 'Status Kelahiran',
                    'data' => [$menyusui, $sudahSapih],
                    'backgroundColor' => ['#FFA500', '#4CAF50'],
                    'borderColor' => ['#FF8C00', '#45a049'],
                ],
            ],
            'labels' => ['Menyusui', 'Sudah Sapih'],
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