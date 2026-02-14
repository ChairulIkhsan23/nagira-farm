<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TernakResource\Pages;
use App\Filament\Resources\TernakResource\RelationManagers;
use App\Models\Ternak;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TernakResource extends Resource
{
    protected static ?string $model = Ternak::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('slug')
                    ->required(),
                Forms\Components\TextInput::make('kode_ternak')
                    ->required(),
                Forms\Components\TextInput::make('nama_ternak'),
                Forms\Components\TextInput::make('jenis_ternak')
                    ->required(),
                Forms\Components\TextInput::make('kategori'),
                Forms\Components\TextInput::make('jenis_kelamin')
                    ->required(),
                Forms\Components\DatePicker::make('tanggal_lahir'),
                Forms\Components\TextInput::make('foto'),
                Forms\Components\TextInput::make('status_aktif')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kode_ternak')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_ternak')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis_ternak')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kategori')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis_kelamin')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_lahir')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('foto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status_aktif')
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
            'index' => Pages\ListTernaks::route('/'),
            'create' => Pages\CreateTernak::route('/create'),
            'edit' => Pages\EditTernak::route('/{record}/edit'),
        ];
    }
}
