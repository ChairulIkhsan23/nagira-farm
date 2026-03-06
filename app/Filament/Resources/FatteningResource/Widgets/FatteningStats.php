<?php

namespace App\Filament\Resources\FatteningResource\Widgets;

use App\Models\Fattening;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FatteningStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $total = Fattening::count();
        $aktif = Fattening::aktif()->count();
        $selesai = Fattening::selesai()->count();
        $gagal = Fattening::gagal()->count();
        $overdue = Fattening::overdue()->count();

        return [

            Stat::make('Total Program', $total)
                ->description('Jumlah semua program fattening')
                ->color('primary'),

            Stat::make('Program Aktif', $aktif)
                ->description('Sedang berjalan')
                ->color('warning'),

            Stat::make('Selesai', $selesai)
                ->description('Program berhasil')
                ->color('success'),

            Stat::make('Gagal', $overdue)
                ->description('Melewati target')
                ->color($overdue > 0 ? 'danger' : 'success'),

        ];
    }
}