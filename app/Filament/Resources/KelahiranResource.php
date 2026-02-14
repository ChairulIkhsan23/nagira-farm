<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KelahiranResource\Pages;
use App\Filament\Resources\KelahiranResource\RelationManagers;
use App\Models\Kelahiran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KelahiranResource extends Resource
{
    protected static ?string $model = Kelahiran::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('betina_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('perkawinan_id')
                    ->numeric(),
                Forms\Components\DatePicker::make('tanggal_melahirkan')
                    ->required(),
                Forms\Components\TextInput::make('jumlah_anak_lahir')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('jumlah_anak_hidup')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('jumlah_anak_mati')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('keterangan'),
                Forms\Components\Textarea::make('detail_anak')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('betina_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('perkawinan_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_melahirkan')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah_anak_lahir')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah_anak_hidup')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah_anak_mati')
                    ->numeric()
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
            'index' => Pages\ListKelahirans::route('/'),
            'create' => Pages\CreateKelahiran::route('/create'),
            'edit' => Pages\EditKelahiran::route('/{record}/edit'),
        ];
    }
}
