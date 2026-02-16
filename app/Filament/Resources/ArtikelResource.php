<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArtikelResource\Pages;
use App\Models\Artikel;

// Filament Core
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;

// Forms
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;

// Tables
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

// Table Actions
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;

// Laravel
use Illuminate\Database\Eloquent\Builder;

class ArtikelResource extends Resource
{
    protected static ?string $model = Artikel::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationGroup = 'Konten';
    protected static ?string $navigationLabel = 'Artikel';
    protected static ?string $modelLabel = 'Artikel';
    protected static ?string $pluralModelLabel = 'Daftar Artikel';

    // ===================== FORM =====================
    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Informasi Artikel')
                ->schema([

                    TextInput::make('judul')
                        ->required()
                        ->maxLength(255)
                        ->live(debounce: 500),

                    TextInput::make('slug')
                        ->disabled()
                        ->dehydrated()
                        ->helperText('Slug dibuat otomatis dari judul')
                        ->formatStateUsing(fn ($state, $record) =>
                            $state ?? str()->slug($record?->judul ?? '')
                        ),

                    Select::make('kategori_id')
                        ->relationship('kategori', 'nama_kategori')
                        ->required()
                        ->searchable(),

                    FileUpload::make('foto')
                        ->image()
                        ->directory('artikel'),

                    RichEditor::make('isi')
                        ->required()
                        ->columnSpanFull(),

                    Select::make('status')
                        ->options([
                            'draft' => 'Draft',
                            'publish' => 'Publish',
                        ])
                        ->required(),

                    DateTimePicker::make('tanggal_publish')
                        ->label('Tanggal Publish'),
                ]),
        ]);
    }

    // ===================== TABLE =====================
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('judul')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('kategori.nama_kategori')
                    ->label('Kategori')
                    ->sortable(),

                BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'draft',
                        'success' => 'publish',
                    ]),

                TextColumn::make('tanggal_publish')
                    ->dateTime('d M Y')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->modalDescription('Yakin ingin menghapus artikel ini?')
                    ->modalSubmitActionLabel('Ya, Hapus')
                    ->modalCancelActionLabel('Batal'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArtikels::route('/'),
            'create' => Pages\CreateArtikel::route('/create'),
            'edit' => Pages\EditArtikel::route('/{record}/edit'),
        ];
    }
}
