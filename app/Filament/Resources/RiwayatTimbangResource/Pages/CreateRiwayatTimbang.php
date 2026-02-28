<?php

namespace App\Filament\Resources\RiwayatTimbangResource\Pages;

use App\Filament\Resources\RiwayatTimbangResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRiwayatTimbang extends CreateRecord
{
    protected static string $resource = RiwayatTimbangResource::class;

    public function getTitle(): string
    {
        return 'Tambah Riwayat Timbang';
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('back')
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
        // redirect ke halaman daftar pengguna setelah pembuatan
        return $this->getResource()::getUrl('index');
    }
}
