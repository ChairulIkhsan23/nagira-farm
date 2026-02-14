<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PakanResource\Pages;
use App\Filament\Resources\PakanResource\RelationManagers;
use App\Models\Pakan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PakanResource extends Resource
{
    protected static ?string $model = Pakan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('ternak_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('jenis_pakan')
                    ->required(),
                Forms\Components\TextInput::make('jumlah_pakan')
                    ->required()
                    ->numeric(),
                Forms\Components\DateTimePicker::make('tanggal_pemberian')
                    ->required(),
                Forms\Components\Textarea::make('catatan')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ternak_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jenis_pakan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jumlah_pakan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_pemberian')
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
            'index' => Pages\ListPakans::route('/'),
            'create' => Pages\CreatePakan::route('/create'),
            'edit' => Pages\EditPakan::route('/{record}/edit'),
        ];
    }
}
