<?php

namespace App\Filament\Resources\FatteningResource\Pages;

use App\Filament\Resources\FatteningResource;
use App\Filament\Resources\FatteningResource\Widgets\WeightProgressChart;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFattening extends ViewRecord
{
    protected static string $resource = FatteningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        $widgets = [];

        if ($this->record->riwayatTimbangs()->exists()) {
            $widgets[] = WeightProgressChart::class;
        }

        return $widgets;
    }
}