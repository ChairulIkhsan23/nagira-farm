<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FatteningResource\Pages;
use App\Filament\Resources\FatteningResource\RelationManagers;
use App\Models\Fattening;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FatteningResource extends Resource
{
    protected static ?string $model = Fattening::class;

    protected static ?string $navigationIcon = 'heroicon-o-chevron-double-up';
    protected static ?string $navigationGroup = 'Program Penggemukan';
    protected static ?string $navigationLabel = 'Fattening';
    protected static ?string $modelLabel = 'Fattening';
    protected static ?string $pluralModelLabel = 'Daftar Fattening';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('ternak_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('bobot_awal')
                    ->numeric(),
                Forms\Components\TextInput::make('bobot_terakhir')
                    ->numeric(),
                Forms\Components\TextInput::make('target_bobot')
                    ->numeric(),
                Forms\Components\DatePicker::make('tanggal_mulai'),
                Forms\Components\DatePicker::make('tanggal_target_selesai'),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\Textarea::make('keterangan')
                    ->required()
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
                Tables\Columns\TextColumn::make('bobot_awal')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bobot_terakhir')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('target_bobot')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_mulai')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_target_selesai')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
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
            'index' => Pages\ListFattenings::route('/'),
            'create' => Pages\CreateFattening::route('/create'),
            'edit' => Pages\EditFattening::route('/{record}/edit'),
        ];
    }
}
