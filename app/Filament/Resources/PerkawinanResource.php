<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PerkawinanResource\Pages;
use App\Filament\Resources\PerkawinanResource\RelationManagers;
use App\Models\Perkawinan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PerkawinanResource extends Resource
{
    protected static ?string $model = Perkawinan::class;

    protected static ?string $navigationIcon = 'heroicon-o-heart';
    protected static ?string $navigationGroup = 'Program Perkawinan';
    protected static ?string $navigationLabel = 'Breeding';
    protected static ?string $modelLabel = 'Breeding';
    protected static ?string $pluralModelLabel = 'Daftar Breeding';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('betina_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('pejantan_id')
                    ->numeric(),
                Forms\Components\DatePicker::make('tanggal_kawin'),
                Forms\Components\TextInput::make('jenis_kawin')
                    ->required(),
                Forms\Components\TextInput::make('status_siklus')
                    ->required(),
                Forms\Components\DatePicker::make('perkiraan_lahir'),
                Forms\Components\TextInput::make('keterangan'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('betina_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pejantan_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_kawin')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jenis_kawin')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status_siklus')
                    ->searchable(),
                Tables\Columns\TextColumn::make('perkiraan_lahir')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('keterangan')
                    ->searchable(),
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
            'index' => Pages\ListPerkawinans::route('/'),
            'create' => Pages\CreatePerkawinan::route('/create'),
            'edit' => Pages\EditPerkawinan::route('/{record}/edit'),
        ];
    }
}
