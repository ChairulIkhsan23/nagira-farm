<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KategoriArtikelResource\Pages;
use App\Models\KategoriArtikel;

// Filament Core
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;

// Forms
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;

// Tables
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

// Tables Actions
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;

// Laravel
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KategoriArtikelResource extends Resource
{
    protected static ?string $model = KategoriArtikel::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Kategori Artikel';

    protected static ?string $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Kategori';
    
    protected static ?string $pluralModelLabel = 'Kategori Artikel';

    // ===================== FORM =====================
    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Informasi Kategori')
                ->description('Slug dibuat otomatis dari nama kategori')
                ->schema([
                    TextInput::make('nama_kategori')
                        ->label('Nama Kategori')
                        ->required()
                        ->maxLength(150)
                        ->live(debounce: 500),

                    TextInput::make('slug')
                        ->disabled()
                        ->dehydrated()
                        ->helperText('Slug dibuat otomatis')
                        ->formatStateUsing(fn ($state, $record) =>
                            $state ?? str()->slug($record?->nama_kategori ?? '')
                        ),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_kategori')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('slug')
                    ->copyable()
                    ->copyMessage('Slug disalin')
                    ->color('gray')
                    ->toggleable(),

                BadgeColumn::make('total_artikels_count')
                    ->label('Total Artikel')
                    ->getStateUsing(fn ($record) => $record->total_artikels_count)
                    ->color('primary'),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->label('Dihapus')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->modalDescription('Apakah Anda yakin ingin menghapus kategori ini? Data tidak dapat dikembalikan.')
                    ->modalSubmitActionLabel('Ya, Hapus')
                    ->modalCancelActionLabel('Batal'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKategoriArtikels::route('/'),
            'create' => Pages\CreateKategoriArtikel::route('/create'),
            'edit' => Pages\EditKategoriArtikel::route('/{record}/edit'),
        ];
    }
}
