<?php

namespace App\Filament\Resources\KategoriArtikelResource\Pages;

use App\Filament\Resources\KategoriArtikelResource;
use App\Filament\Resources\KategoriArtikelResource\Widgets\KategoriArtikelStats;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKategoriArtikels extends ListRecords
{
    protected static string $resource = KategoriArtikelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Kategori Artikel')
                ->icon('heroicon-o-plus'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            KategoriArtikelStats::class,
        ];
    }
}
