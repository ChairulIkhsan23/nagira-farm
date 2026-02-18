<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PakanTernakResource\Pages;
use App\Models\PakanTernak;
use App\Models\Pakan;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

// Form
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;

// Table
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;

class PakanTernakResource extends Resource
{
    protected static ?string $model = PakanTernak::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Manajemen Pakan';
    protected static ?string $navigationLabel = 'Pakan Ternak';
    protected static ?string $modelLabel = 'Pakan Ternak';
    protected static ?string $pluralModelLabel = 'Riwayat Pakan Ternak';

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Section::make('Input Pemberian Pakan')
                ->description('Catat pakan yang diberikan ke ternak')
                ->icon('heroicon-o-cube')
                ->schema([

                    Grid::make(2)->schema([

                        // =====================
                        // SELECT TERNAK
                        // =====================
                        Select::make('ternak_id')
                            ->label('Ternak')
                            ->relationship('ternak', 'kode_ternak')
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {

                                $ternak = \App\Models\Ternak::find($state);

                                $set('ternak_nama_preview', $ternak?->nama_ternak);
                                $set('ternak_kode_preview', $ternak?->kode_ternak);
                            })
                            ->required()
                            ->columnSpan(1),

                        // =====================
                        // SELECT PAKAN
                        // =====================
                        Select::make('pakan_id')
                            ->label('Pakan')
                            ->relationship('pakan', 'kode_pakan')
                            ->getOptionLabelFromRecordUsing(fn ($record) =>
                                $record->kode_pakan . ' | ' .
                                $record->nama_pakan .
                                ' (Stok: ' . $record->stok . ' ' . $record->satuan . ')'
                            )
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {

                                $pakan = \App\Models\Pakan::find($state);

                                $set('pakan_nama_preview', $pakan?->nama_pakan);
                                $set('pakan_stok_preview', $pakan?->stok);
                                $set('pakan_satuan_preview', $pakan?->satuan);
                            })
                            ->required()
                            ->columnSpan(1),

                    ]),

                    // =====================
                    // PREVIEW TERNAK
                    // =====================
                    Section::make('Detail Ternak')
                        ->schema([
                            Placeholder::make('ternak_kode_preview')
                                ->label('Kode Ternak')
                                ->content(fn ($get) => $get('ternak_kode_preview') ?? '-'),

                            Placeholder::make('ternak_nama_preview')
                                ->label('Nama Ternak')
                                ->content(fn ($get) => $get('ternak_nama_preview') ?? '-'),
                        ])
                        ->columns(2)
                        ->collapsible(),

                    // =====================
                    // PREVIEW PAKAN
                    // =====================
                    Section::make('Detail Pakan')
                        ->schema([
                            Placeholder::make('pakan_nama_preview')
                                ->label('Nama Pakan')
                                ->content(fn ($get) => $get('pakan_nama_preview') ?? '-'),

                            Placeholder::make('pakan_stok_preview')
                                ->label('Stok Saat Ini')
                                ->content(fn ($get) => 
                                    ($get('pakan_stok_preview') ?? 0) . ' ' . ($get('pakan_satuan_preview') ?? '')
                                ),
                        ])
                        ->columns(2)
                        ->collapsible(),

                    // =====================
                    // JUMLAH
                    // =====================
                    TextInput::make('jumlah')
                        ->numeric()
                        ->required()
                        ->minValue(0.1)
                        ->suffix(fn ($get) => $get('pakan_satuan_preview') ?? ''),
                    // =====================
                    // TANGGAL
                    // =====================
                    DatePicker::make('tanggal')
                        ->required()
                        ->default(now())
                        ->native(false)
                        ->displayFormat('d M Y'),

                ]),
        ]);
}
    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('ternak.id')
                    ->label('ID Ternak')
                    ->toggleable()
                    ->sortable(),

                TextColumn::make('ternak.kode_ternak')
                    ->label('Kode Ternak')
                    ->weight('bold')
                    ->color('primary')
                    ->sortable()
                    ->toggleable()
                    ->searchable(),

                TextColumn::make('ternak.nama_ternak')
                    ->label('Nama Ternak')
                    ->toggleable()
                    ->searchable(),

                TextColumn::make('pakan.id')
                    ->label('ID Pakan')
                    ->toggleable()
                    ->sortable(),

                TextColumn::make('pakan.kode_pakan')
                    ->label('Kode Pakan')
                    ->weight('bold')
                    ->color('success')
                    ->toggleable()
                    ->sortable(),

                TextColumn::make('pakan.nama_pakan')
                    ->label('Nama Pakan')
                    ->toggleable()
                    ->searchable(),

                TextColumn::make('jumlah')
                ->sortable()
                ->formatStateUsing(fn ($state, $record) =>
                    $state . ' ' . $record->pakan?->satuan
                )
                ->badge()
                ->color(fn ($state) => match (true) {
                    $state <= 5 => 'warning',
                    $state >= 20 => 'info',
                    default => 'success',
                }),
                TextColumn::make('tanggal')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([

                SelectFilter::make('ternak_id')
                    ->relationship('ternak', 'kode_ternak')
                    ->label('Filter Ternak')
                    ->searchable(),

                SelectFilter::make('pakan_id')
                    ->relationship('pakan', 'kode_pakan')
                    ->label('Filter Pakan')
                    ->searchable(),

                Filter::make('tanggal')
                    ->form([
                        DatePicker::make('dari'),
                        DatePicker::make('sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari'],
                                fn ($query, $date) =>
                                    $query->whereDate('tanggal', '>=', $date),
                            )
                            ->when(
                                $data['sampai'],
                                fn ($query, $date) =>
                                    $query->whereDate('tanggal', '<=', $date),
                            );
                    }),
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
            ->defaultSort('tanggal', 'desc')
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPakanTernaks::route('/'),
            'create' => Pages\CreatePakanTernak::route('/create'),
            'edit' => Pages\EditPakanTernak::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereDate('tanggal', today())->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }
public static function getHeaderWidgets(): array
{
    return [
        \App\Filament\Resources\PakanTernakResource\Widgets\PakanTernakChart::class,
    ];
}
}
