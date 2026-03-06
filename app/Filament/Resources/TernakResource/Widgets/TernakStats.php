<?php

namespace App\Filament\Resources\TernakResource\Widgets;

use App\Models\Ternak;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TernakStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [

            Stat::make('Total Ternak', Ternak::count())
                ->description('Semua ternak terdaftar')
                ->icon('heroicon-o-archive-box')
                ->color('primary'),

            Stat::make('Ternak Aktif', Ternak::where('status_aktif','aktif')->count())
                ->description('Masih dipelihara')
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Ternak Terjual', Ternak::where('status_aktif','terjual')->count())
                ->description('Sudah dijual')
                ->icon('heroicon-o-banknotes')
                ->color('warning'),

            Stat::make('Ternak Mati', Ternak::where('status_aktif','mati')->count())
                ->description('Kematian ternak')
                ->icon('heroicon-o-x-circle')
                ->color('danger'),

            Stat::make('Jantan', Ternak::where('jenis_kelamin','jantan')->count())
                ->description('Jumlah pejantan')
                ->icon('heroicon-o-arrow-up')
                ->color('info'),

            Stat::make('Betina', Ternak::where('jenis_kelamin','betina')->count())
                ->description('Jumlah indukan')
                ->icon('heroicon-o-arrow-down')
                ->color('warning'),
        ];
    }
}