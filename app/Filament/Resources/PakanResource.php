<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PakanResource\Pages;
use App\Models\Pakan;
use App\Enums\JenisPakan;
use App\Enums\NamaPakan;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;

use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;

class PakanResource extends Resource
{
    protected static ?string $model = Pakan::class;

    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';
    protected static ?string $navigationGroup = 'Manajemen Pakan';
    protected static ?string $navigationLabel = 'Pakan';
    protected static ?string $modelLabel = 'Pakan';
    protected static ?string $pluralModelLabel = 'Daftar Pakan';

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Section::make('Informasi Data Pakan')
                ->description('Data utama pakan ternak')
                ->icon('heroicon-o-circle-stack')
                ->schema([

                    Grid::make(2)
                        ->schema([

                            Select::make('jenis_pakan')
                                ->label('Jenis Pakan')
                                ->options(JenisPakan::options())
                                ->searchable()
                                ->reactive()
                                ->required()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    $set('kode_pakan', \App\Models\Pakan::generateKodePakan($state));
                                    $set('nama_pakan', null);
                                })
                                ->columnSpan(1),

                            TextInput::make('kode_pakan')
                                ->label('Kode Pakan')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->disabled()
                                ->dehydrated()
                                ->helperText('Kode otomatis berdasarkan jenis pakan')
                                ->columnSpan(1),

                            Hidden::make('slug'),
                        ]),

                    Grid::make(2)
                        ->schema([

                            Select::make('nama_pakan')
                                ->label('Nama Pakan')
                                ->options(fn (callable $get) =>
                                    NamaPakan::optionsByJenis($get('jenis_pakan'))
                                )
                                ->required()
                                ->disabled(fn (callable $get) =>
                                    !$get('jenis_pakan')
                                )
                                ->columnSpan(1),

                            TextInput::make('stok')
                                ->label('Stok')
                                ->numeric()
                                ->required()
                                ->default(0)
                                ->minValue(0)
                                ->suffix(fn (callable $get) => $get('satuan') ?? '')
                                ->columnSpan(1),
                        ]),

                    Grid::make(2)
                        ->schema([

                            Select::make('satuan')
                                ->options([
                                    'kg' => 'Kg',
                                    'gram' => 'Gram',
                                    'ton' => 'Ton',
                                    'karung' => 'Karung',
                                    'sak' => 'Sak',
                                    'ikat' => 'Ikat',
                                    'liter' => 'Liter',
                                ])
                                ->required()
                                ->native(false)
                                ->columnSpan(1),

                            Textarea::make('catatan')
                                ->rows(3)
                                ->columnSpan(1),
                        ]),
                ]),
        ]);
}
    public static function table(Table $table): Table
{
    return $table
        ->columns([

            TextColumn::make('kode_pakan')
                ->label('Kode')
                ->searchable()
                ->copyable()
                ->copyMessage('Kode pakan copied')
                ->weight('bold')
                ->color('primary')
                ->toggleable()
                ->sortable(),

            TextColumn::make('jenis_pakan')
                ->badge()
                ->color('info')
                ->searchable()
                ->toggleable()
                ->sortable(),

            TextColumn::make('nama_pakan')
                ->searchable()
                ->toggleable()
                ->sortable(),

            TextColumn::make('stok')
                ->sortable()
                ->formatStateUsing(fn ($state, $record) =>
                    $state . ' ' . $record->satuan
                )
                ->color(fn ($state) => match (true) {
                    $state <= 0 => 'danger',
                    $state <= 10 => 'warning',
                    default => 'success',
                })
                ->badge()
                ->toggleable(),

            TextColumn::make('created_at')
                ->dateTime('d M Y H:i')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->filters([

            SelectFilter::make('jenis_pakan')
                ->options(JenisPakan::options())
                ->searchable(),

            Filter::make('stok_menipis')
                ->query(fn ($query) => $query->where('stok', '<=', 10))
                ->label('Stok ≤ 10'),

            Filter::make('stok_habis')
                ->query(fn ($query) => $query->where('stok', '<=', 0))
                ->label('Stok Habis'),
        ])
        ->actions([
            ActionGroup::make([
                EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->color('primary'),

                DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation(),
            ])
        ])
        ->bulkActions([
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ])
        ->defaultSort('created_at', 'desc')
        ->striped();
}


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPakans::route('/'),
            'create' => Pages\CreatePakan::route('/create'),
            'edit' => Pages\EditPakan::route('/{record}/edit'),
            ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }
        
}
        