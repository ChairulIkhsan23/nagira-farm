<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KesehatanResource\Pages;
use App\Filament\Resources\KesehatanResource\RelationManagers;
use App\Models\Kesehatan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KesehatanResource extends Resource
{
    protected static ?string $model = Kesehatan::class;

    protected static ?string $navigationIcon = 'heroicon-o-face-smile';
    protected static ?string $navigationGroup = 'Manajemen Ternak';
    protected static ?string $navigationLabel = 'Kesehatan';
    protected static ?string $modelLabel = 'Kesehatan';
    protected static ?string $pluralModelLabel = 'Daftar Kesehatan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('ternak_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('kondisi')
                    ->required(),
                Forms\Components\TextInput::make('diagnosa'),
                Forms\Components\Textarea::make('tindakan')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('obat'),
                Forms\Components\DateTimePicker::make('tanggal_periksa'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ternak_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kondisi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('diagnosa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('obat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_periksa')
                    ->dateTime()
                    ->sortable(),
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
            'index' => Pages\ListKesehatans::route('/'),
            'create' => Pages\CreateKesehatan::route('/create'),
            'edit' => Pages\EditKesehatan::route('/{record}/edit'),
        ];
    }
}
