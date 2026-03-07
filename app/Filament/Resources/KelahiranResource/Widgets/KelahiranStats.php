<?php

namespace App\Filament\Resources\KelahiranResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Kelahiran;
use Carbon\Carbon;

class KelahiranStats extends BaseWidget
{
    protected static ?int $sort = 1; // Tambahkan ini biar bisa diurutkan

    protected function getStats(): array
    {
        $totalKelahiran = Kelahiran::count();
        $totalAnakLahir = Kelahiran::sum('jumlah_anak_lahir');
        $totalAnakHidup = Kelahiran::sum('jumlah_anak_hidup');
        $totalAnakMati = $totalAnakLahir - $totalAnakHidup;
        
        $bulanIni = Kelahiran::whereMonth('tanggal_melahirkan', now()->month)
            ->whereYear('tanggal_melahirkan', now()->year)
            ->count();
            
        $bulanLalu = Kelahiran::whereMonth('tanggal_melahirkan', now()->subMonth()->month)
            ->whereYear('tanggal_melahirkan', now()->subMonth()->year)
            ->count();
        
        $trend = $bulanLalu > 0 ? round((($bulanIni - $bulanLalu) / $bulanLalu) * 100, 1) : 0;
        $rataAnak = $totalKelahiran > 0 ? round($totalAnakLahir / $totalKelahiran, 1) : 0;
        $survivalRate = $totalAnakLahir > 0 ? round(($totalAnakHidup / $totalAnakLahir) * 100, 1) : 0;

        return [
            Stat::make('Total Kelahiran', (string) $totalKelahiran)
                ->description('Jumlah event kelahiran')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),
                
            Stat::make('Total Anak Lahir', (string) $totalAnakLahir)
                ->description($totalAnakHidup . ' hidup, ' . $totalAnakMati . ' mati')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),
                
            Stat::make('Kelahiran Bulan Ini', (string) $bulanIni)
                ->description(($trend > 0 ? '+' : '') . $trend . '% dari bulan lalu')
                ->descriptionIcon($trend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($trend >= 0 ? 'success' : 'danger'),
        ];
    }
    
    protected function getColumns(): int
    {
        return 3; // Ubah dari 5 jadi 3 karena kita cuma punya 3 stat
    }
}