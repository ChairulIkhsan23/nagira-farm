<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RiwayatTimbangResource\Pages;
use App\Filament\Resources\RiwayatTimbangResource\RelationManagers;
use App\Models\RiwayatTimbang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RiwayatTimbangResource extends Resource
{
    protected static ?string $model = RiwayatTimbang::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('ternak_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('bobot')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('fattening_id')
                    ->numeric(),
                Forms\Components\DateTimePicker::make('tanggal_timbang')
                    ->required(),
                Forms\Components\TextInput::make('catatan'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ternak_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bobot')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fattening_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_timbang')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('catatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRiwayatTimbangs::route('/'),
            'create' => Pages\CreateRiwayatTimbang::route('/create'),
            'edit' => Pages\EditRiwayatTimbang::route('/{record}/edit'),
        ];
    }
}
