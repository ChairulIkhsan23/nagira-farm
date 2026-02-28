<?php

namespace App\Filament\Resources\FatteningResource\Pages;

use App\Filament\Resources\FatteningResource;
use App\Models\Fattening;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFattening extends EditRecord
{
    protected static string $resource = FatteningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Hapus Fattening')
                ->icon('heroicon-o-trash')
                ->modalDescription('Apakah Anda yakin ingin menghapus data ini? Data tidak dapat dikembalikan.')
                ->modalSubmitActionLabel('Ya, Hapus')
                ->modalCancelActionLabel('Batal'),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('back')
            ->label('Batal')
            ->color('gray')
            ->url($this->getResource()::getUrl('index'))
            ->icon('heroicon-o-arrow-left'),
            
            $this->getSaveFormAction()
            ->label('Simpan Perubahan')
            ->icon('heroicon-o-check'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // additional methods if needed
    protected function afterSave(): void
    {
        $fattening = $this->record;
        $ternak = $fattening->ternak;
        
        if (!$ternak) return;

        // Update bobot ternak
        $ternak->bobot = $fattening->bobot_terakhir;
        $ternak->tanggal_timbang_terakhir = now();
        
        //Cek Status
        if ($fattening->status === 'selesai' || $fattening->status === 'gagal') {
            // Cek apakah masih ada program aktif lain
            $hasActiveFattening = Fattening::where('ternak_id', $ternak->id)
                ->where('status', 'progres')
                ->where('id', '!=', $fattening->id)
                ->exists();
            
            if (!$hasActiveFattening) {
                $ternak->kategori = 'regular';
            }
        }
        
        $ternak->save();
    }
}
