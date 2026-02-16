<?php

namespace App\Filament\Resources\TernakResource\Pages;

use App\Filament\Resources\TernakResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTernak extends CreateRecord
{
    protected static string $resource = TernakResource::class;

        public function getTitle(): string
    {
        return 'Tambah Ternak';
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
