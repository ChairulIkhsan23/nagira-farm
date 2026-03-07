<?php

namespace App\Filament\Resources\PakanTernakResource\Widgets;

use App\Models\PakanTernak;
use App\Models\Pakan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PakanTernakStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalPemberian = PakanTernak::count();
        
        // Ambil semua pakan dengan berbagai satuan
        $semuaPakan = PakanTernak::with('pakan')->get();
        
        // Kelompokkan berdasarkan satuan
        $totalPerSatuan = [];
        foreach ($semuaPakan as $item) {
            $satuan = $item->pakan?->satuan ?? 'unit';
            $totalPerSatuan[$satuan] = ($totalPerSatuan[$satuan] ?? 0) + $item->jumlah;
        }
        
        // Format total kuantitas jadi ringkas
        $deskripsiTotal = [];
        foreach ($totalPerSatuan as $satuan => $total) {
            // Format angka supaya lebih pendek
            if ($total >= 1000) {
                $total = round($total / 1000, 1) . 'k';
            } else {
                $total = round($total, 1);
            }
            $deskripsiTotal[] = $total . ' ' . $satuan;
        }
        
        // Gabungkan dengan batas maksimal 2 satuan
        if (count($deskripsiTotal) > 2) {
            $deskripsiTotal = array_slice($deskripsiTotal, 0, 2);
            $deskripsiTotal[] = '+' . (count($totalPerSatuan) - 2) . ' lainnya';
        }
        $deskripsiTotal = implode(' + ', $deskripsiTotal);
        
        $hariIni = PakanTernak::whereDate('tanggal', today())->count();
        $bulanIni = PakanTernak::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->count();
        $bulanLalu = PakanTernak::whereMonth('tanggal', now()->subMonth()->month)
            ->whereYear('tanggal', now()->subMonth()->year)
            ->count();
        
        $trend = $bulanLalu > 0 ? round((($bulanIni - $bulanLalu) / $bulanLalu) * 100, 1) : 0;

        return [
            Stat::make('Total Pemberian', (string) $totalPemberian . 'x')
                ->description('Jumlah kali pemberian pakan')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary'),
                
            Stat::make('Total Kuantitas', $deskripsiTotal ?: '0')
                ->description('Total pakan diberikan')
                ->descriptionIcon('heroicon-m-scale')
                ->color('success'),
                
            Stat::make('Pemberian Hari Ini', (string) $hariIni . 'x')
                ->description(($trend > 0 ? '+' : '') . $trend . '% dari bulan lalu')
                ->descriptionIcon($trend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($trend >= 0 ? 'success' : 'danger'),
        ];
    }
    
    protected function getColumns(): int
    {
        return 3;
    }
}