<?php

namespace App\Filament\Resources\PakanTernakResource\Pages;

use App\Filament\Resources\PakanTernakResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePakanTernak extends CreateRecord
{
    protected static string $resource = PakanTernakResource::class;
    public function getTitle(): string
    {
        return 'Tambah Pakan Ternak';
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
