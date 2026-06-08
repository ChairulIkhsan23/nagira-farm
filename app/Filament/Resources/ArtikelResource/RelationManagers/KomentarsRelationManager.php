<?php

namespace App\Filament\Resources\ArtikelResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KomentarsRelationManager extends RelationManager
{
    protected static string $relationship = 'komentars';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email'),

                TextInput::make('isi')
                    ->required()
                    ->maxLength(255),

                Toggle::make('is_approved')
                    ->label('Disetujui')
                    ->helperText('Tandai jika komentar sudah disetujui'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('isi')
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                    
                TextColumn::make('isi'),
                
                TextColumn::make('is_approved')
                    ->label('Disetujui')
                    ->formatStateUsing(fn ($state) => $state ? 'Ya' : 'Tidak')
                    ->sortable(),
            ])
            ->filters([
                
            ])      
            ->headerActions([
                
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
