<?php

namespace App\Filament\Resources\ArtikelResource\Widgets;

use App\Models\Artikel;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class ArtikelReminder extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $now = now();

        $artikelBulanIni = Artikel::where('status', 'publish')
            ->whereMonth('tanggal_publish', $now->month)
            ->whereYear('tanggal_publish', $now->year)
            ->count();

        $artikelTerakhir = Artikel::where('status', 'publish')
            ->latest('tanggal_publish')
            ->first();

        $hariSejakPublish = $artikelTerakhir
            ? Carbon::parse($artikelTerakhir->tanggal_publish)->diffInDays($now)
            : null;

        return [

            Stat::make(
                'Update Bulanan',
                $artikelBulanIni > 0 ? 'Sudah diperbarui' : 'Belum diperbarui'
            )
                ->description(
                    $artikelBulanIni > 0
                        ? 'Artikel bulan ini sudah tersedia'
                        : 'Belum ada artikel yang dipublish bulan ini'
                )
                ->color($artikelBulanIni > 0 ? 'success' : 'danger'),

            Stat::make(
                'Terakhir Publish',
                $hariSejakPublish !== null
                    ? $hariSejakPublish . ' hari lalu'
                    : 'Belum pernah publish'
            )
                ->description('Jarak dari publish terakhir')
                ->color(
                    $hariSejakPublish !== null && $hariSejakPublish > 30
                        ? 'danger'
                        : 'success'
                ),

            Stat::make(
                'Total Publish',
                Artikel::where('status', 'publish')->count()
            )
                ->description('Jumlah artikel aktif')
                ->color('primary'),
        ];
    }
}