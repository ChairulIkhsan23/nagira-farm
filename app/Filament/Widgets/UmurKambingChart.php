<?php

namespace App\Filament\Widgets;

use App\Models\Ternak;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class UmurKambingChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Umur Kambing';

    protected static ?string $maxHeight = '350px';

    protected function getData(): array
    {
        $anak = 0;
        $muda = 0;
        $dewasa = 0;
        $tua = 0;

        $ternaks = Ternak::all();

        foreach ($ternaks as $ternak) {

            $umurBulan = Carbon::parse($ternak->tanggal_lahir)
                ->diffInMonths(now());

            if ($umurBulan <= 6) {
                $anak++;
            } elseif ($umurBulan <= 12) {
                $muda++;
            } elseif ($umurBulan <= 24) {
                $dewasa++;
            } else {
                $tua++;
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Kambing',
                    'data' => [$anak, $muda, $dewasa, $tua],
                    'backgroundColor' => [
                        '#22c55e', // hijau
                        '#3b82f6', // biru
                        '#f59e0b', // kuning
                        '#ef4444', // merah
                    ],
                ],
            ],
            'labels' => [
                '0-6 Bulan',
                '6-12 Bulan',
                '1-2 Tahun',
                '>2 Tahun',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
        ];
    }
}