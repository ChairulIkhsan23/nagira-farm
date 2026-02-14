<?php

namespace App\Filament\Resources\KategoriArtikelResource\Pages;

use App\Filament\Resources\KategoriArtikelResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateKategoriArtikel extends CreateRecord
{
    protected static string $resource = KategoriArtikelResource::class;

    public function getTitle(): string
    {
        return 'Tambah Kategori Artikel';
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('back')
            ->label('Kembali')
            ->color('gray')
            ->url($this->getResource()::getUrl('index'))
            ->icon('heroicon-o-arrow-left'),
            
            $this->getCreateFormAction()
            ->label('Simpan')
            ->icon('heroicon-o-check'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        // redirect ke halaman daftar kategiri artikel setelah pembuatan
        return $this->getResource()::getUrl('index');
    }
}
