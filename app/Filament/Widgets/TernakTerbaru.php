<?php

namespace App\Filament\Widgets;

use App\Models\Ternak;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TernakTerbaru extends BaseWidget
{
    protected static ?string $heading = '5 Ternak Terbaru';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Ternak::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('kode_ternak')
                    ->label('Kode')
                    ->searchable(),

                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama'),

                Tables\Columns\TextColumn::make('jenis_ternak')
                    ->label('Jenis'),

                Tables\Columns\TextColumn::make('jenis_kelamin')
                    ->label('Kelamin')
                    ->badge(),

                Tables\Columns\TextColumn::make('berat')
                    ->label('Berat (Kg)')
                    ->suffix(' Kg'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ditambahkan')
                    ->date('d M Y'),
            ]);
    }

    // DIPERBESAR jadi 400px atau 500px
    protected static ?string $maxHeight = '400px';
    
    // Full width biar lebih dominan
    protected int | string | array $columnSpan = 'full';
}