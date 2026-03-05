<?php

namespace App\Filament\Resources\KategoriArtikelResource\Widgets;

use App\Models\KategoriArtikel;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class KategoriArtikelStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalKategori = KategoriArtikel::count();

        $totalArtikel = KategoriArtikel::withCount('artikels')
            ->get()
            ->sum('artikels_count');

        $kategoriPopuler = KategoriArtikel::withCount('artikels')
            ->orderByDesc('artikels_count')
            ->first();

        $kategoriKosong = KategoriArtikel::doesntHave('artikels')->count();

        return [

            Stat::make('Total Kategori', $totalKategori)
                ->description('Jumlah kategori artikel')
                ->color('primary'),

            Stat::make(
                'Kategori Terpopuler',
                $kategoriPopuler ? $kategoriPopuler->nama_kategori : 'Belum ada'
            )
                ->description(
                    $kategoriPopuler
                        ? $kategoriPopuler->artikels_count . ' artikel'
                        : 'Tidak ada artikel'
                )
                ->color('info'),

            Stat::make('Kategori Kosong', $kategoriKosong)
                ->description('Kategori tanpa artikel')
                ->color($kategoriKosong > 0 ? 'warning' : 'success'),

        ];
    }
}