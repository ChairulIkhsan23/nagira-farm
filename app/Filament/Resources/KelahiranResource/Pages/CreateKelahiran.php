<?php

namespace App\Filament\Resources\KelahiranResource\Pages;

use App\Filament\Resources\KelahiranResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateKelahiran extends CreateRecord
{
    protected static string $resource = KelahiranResource::class;
    public function getTitle(): string
    {
        return 'Tambah Kelahiran';
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
        return $this->getResource()::getUrl('index');
    }
}
