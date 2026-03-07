<?php

namespace App\Filament\Resources\KelahiranResource\Pages;

use App\Filament\Resources\KelahiranResource\Widgets\KelahiranStats;
use App\Filament\Resources\KelahiranResource\Widgets\KelahiranStatusChart;
use App\Filament\Resources\KelahiranResource\Widgets\KelahiranTrendChart;
use App\Filament\Resources\KelahiranResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKelahirans extends ListRecords
{
    protected static string $resource = KelahiranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Kelahiran Baru')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getHeaderWidgets(): array
    {
        return [
            KelahiranStats::class,
            KelahiranStatusChart::class,
            KelahiranTrendChart::class,
        ];
    }

    // Tambahkan method ini untuk mengatur jumlah kolom widget
    public function getHeaderWidgetsColumns(): int | array
    {
        return [
            'default' => 1, // Default 1 kolom
            'sm' => 1,      // Small screen 1 kolom
            'md' => 2,      // Medium screen 2 kolom (stats 1 baris, chart 2 kolom)
            'lg' => 2,      // Large screen 2 kolom
            'xl' => 2,      // Extra large screen 2 kolom
        ];
    }
}