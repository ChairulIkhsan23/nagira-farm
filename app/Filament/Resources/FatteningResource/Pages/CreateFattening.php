<?php

namespace App\Filament\Resources\FatteningResource\Pages;

use App\Filament\Resources\FatteningResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFattening extends CreateRecord
{
    protected static string $resource = FatteningResource::class;

    public function getTitle(): string
    {
        return 'Tambah Fattening';
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

    // additional methods if needed
    protected function afterCreate(): void
    {
        $this->record->ternak?->update([
            'kategori' => 'fattening',
            'bobot' => $this->record->bobot_awal,
            'tanggal_timbang_terakhir' => now(),
        ]);
    }
}
