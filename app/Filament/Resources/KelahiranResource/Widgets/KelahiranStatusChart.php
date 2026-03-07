<?php

namespace App\Filament\Resources\KelahiranResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Kelahiran;

class KelahiranStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Status Kelahiran';
    
    // Tambahkan ini untuk memastikan widget unique
    protected static string $chartId = 'kelahiran-status-chart';

    protected function getData(): array
    {
        // Hitung total kelahiran dengan anak hidup vs mati
        $totalLahir = Kelahiran::sum('jumlah_anak_lahir') ?: 1;
        $totalHidup = Kelahiran::sum('jumlah_anak_hidup');
        $totalMati = Kelahiran::sum('jumlah_anak_mati');
        
        // Hitung jumlah kelahiran (event) berdasarkan status
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