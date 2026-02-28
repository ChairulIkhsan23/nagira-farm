<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PerkawinanResource\Pages;
use App\Models\Perkawinan;
use App\Models\Ternak;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

// Form Components
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

// Table Components
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;

class PerkawinanResource extends Resource
{
    protected static ?string $model = Perkawinan::class;

    protected static ?string $navigationIcon = 'heroicon-o-heart';
    protected static ?string $navigationGroup = 'Program Perkawinan';
    protected static ?string $navigationLabel = 'Perkawinan';
    protected static ?string $modelLabel = 'Perkawinan';
    protected static ?string $pluralModelLabel = 'Data Perkawinan';

    // ================= FORM ================= //

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Informasi Perkawinan')
                ->description('Data siklus reproduksi ternak')
                ->icon('heroicon-o-heart')
                ->schema([

                    Grid::make(2)->schema([

                        // BETINA
                        Select::make('betina_id')
                            ->label('Induk Betina')
                            ->relationship(
                                'betina',
                                'kode_ternak',
                                fn (Builder $query) => $query
                                    ->where('jenis_kelamin', 'Betina')
                                    ->whereDate('tanggal_lahir', '<=', now()->subMonths(10)) 
                                    ->whereDoesntHave('perkawinanSebagaiBetina', function ($q) {
                                        $q->whereIn('status_siklus', ['kawin', 'bunting']);
                                    })
                                    ->whereDoesntHave('kelahirans', function ($q) {
        $q->whereDate('tanggal_sapih', '>', now());
    })
                            )
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {

                                $ternak = \App\Models\Ternak::find($state);
                                $umur = null;
                                $warning = null;

                                if ($ternak && $ternak->tanggal_lahir) {
                                    $diff = \Carbon\Carbon::parse($ternak->tanggal_lahir)->diff(now());
                                    $umur = $diff->y * 12 + $diff->m;

                                    // WARNING REPRODUKSI
                                    if ($umur < 10) {
                                        $warning = "⚠️ Umur betina belum layak kawin (<10 bulan)";
                                    } elseif ($umur > 60) {
                                        $warning = "⚠️ Betina sudah tua untuk breeding";
                                    }
                                }

                                $set('betina_kode', $ternak?->kode_ternak);
                                $set('betina_nama', $ternak?->nama_ternak);
                                $set('betina_umur', $umur ? $umur . ' bulan' : null);
                                $set('betina_warning', $warning);
                            })
                            
                            ->required(),
                        // PEJANTAN
                        Select::make('pejantan_id')
                        ->label('Pejantan')
                        ->relationship(
                            'pejantan',
                            'kode_ternak',
                            fn (Builder $query) => $query
                                ->where('jenis_kelamin', 'Jantan')
                                ->whereDate('tanggal_lahir', '<=', now()->subMonths(10)) // MIN 10 BULAN
                                ->whereDoesntHave('perkawinanSebagaiPejantan', function ($q) {
                                    $q->whereIn('status_siklus', ['kawin', 'bunting']);
                                })
                        )
                        ->searchable()
                        ->preload()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {

                            $ternak = \App\Models\Ternak::find($state);
                            $umur = null;
                            $warning = null;

                            if ($ternak && $ternak->tanggal_lahir) {
                                $diff = \Carbon\Carbon::parse($ternak->tanggal_lahir)->diff(now());
                                $umur = $diff->y * 12 + $diff->m;

                                if ($umur < 10) {
                                    $warning = "⚠️ Pejantan belum matang seksual (<10 bulan)";
                                } elseif ($umur > 120) {
                                    $warning = "⚠️ Pejantan terlalu tua untuk performa optimal";
                                }
                            }

                            $set('pejantan_kode', $ternak?->kode_ternak);
                            $set('pejantan_nama', $ternak?->nama_ternak);
                            $set('pejantan_umur', $umur ? $umur . ' bulan' : null);
                            $set('pejantan_warning', $warning);
                        })
                        ->nullable(),
                    ]),
                    // ===== STATE STORAGE (WAJIB) =====
                    TextInput::make('betina_warning')->hidden(),
                    TextInput::make('pejantan_warning')->hidden(),
                    TextInput::make('betina_kode')->hidden(),
                    TextInput::make('betina_nama')->hidden(),
                    TextInput::make('betina_umur')->hidden(),
                    TextInput::make('pejantan_kode')->hidden(),
                    TextInput::make('pejantan_nama')->hidden(),
                    TextInput::make('pejantan_umur')->hidden(),
                    Section::make('Detail Induk Betina')
                        ->schema([
                            Placeholder::make('betina_kode')
                                ->label('Kode Betina')
                                ->content(fn ($get) => $get('betina_kode') ?? '-'),

                            Placeholder::make('betina_nama')
                                ->label('Nama Betina')
                                ->content(fn ($get) => $get('betina_nama') ?? '-'),

                            Placeholder::make('betina_umur')
                                ->label('Umur (bulan)')
                                ->content(fn ($get) => $get('betina_umur') ?? '-'),
                            Placeholder::make('betina_warning')
                            ->label('Peringatan Reproduksi')
                            ->content(fn ($get) => $get('betina_warning') ?? '-')
                            ->extraAttributes(['class' => 'text-danger font-bold']),
                        ])
                        ->columns(2)
                        ->collapsible(),
                Section::make('Detail Pejantan')
                ->schema([
                    Placeholder::make('pejantan_kode')
                        ->label('Kode Pejantan')
                        ->content(fn ($get) => $get('pejantan_kode') ?? '-'),

                    Placeholder::make('pejantan_nama')
                        ->label('Nama Pejantan')
                        ->content(fn ($get) => $get('pejantan_nama') ?? '-'),

                    Placeholder::make('pejantan_umur')
                        ->label('Umur (bulan)')
                        ->content(fn ($get) => $get('pejantan_umur') ?? '-'),
                    Placeholder::make('pejantan_warning')
                    ->label('Peringatan Reproduksi')
                    ->content(fn ($get) => $get('pejantan_warning') ?? '-')
                    ->extraAttributes(['class' => 'text-danger font-bold']),
                ])
                ->columns(2)
                ->collapsible(),
                    Grid::make(2)->schema([
                        DatePicker::make('tanggal_kawin')
                            ->label('Tanggal Kawin')
                            ->native(false)
                            ->required(),

                        Select::make('jenis_kawin')
                            ->label('Metode Kawin')
                            ->options([
                                'alami' => 'Alami',
                                'IB' => 'Inseminasi Buatan (IB)',
                            ])
                            ->native(false)
                            ->required(),
                    ]),

                    Grid::make(2)->schema([
                        Select::make('status_siklus')
                            ->label('Status Siklus')
                            ->options([
                                'kosong' => 'Kosong',
                                'kawin' => 'Kawin',
                                'bunting' => 'Bunting',
                                'gagal' => 'Gagal',
                                'melahirkan' => 'Melahirkan',
                            ])
                            ->default('kawin')
                            ->native(false),

                        DatePicker::make('perkiraan_lahir')
                            ->label('Perkiraan Lahir')
                            ->native(false)
                            ->disabled()
                            ->dehydrated(),
                    ]),

                    Textarea::make('keterangan')
                        ->columnSpanFull()
                        ->placeholder('Catatan reproduksi...'),
                ])
        ]);
    }

    // ================= TABLE ================= //

    public static function table(Table $table): Table
{
    return $table
        ->columns([

            TextColumn::make('betina.kode_ternak')
                ->label('Induk Betina')
                ->searchable()
                ->sortable()
                ->weight('bold')
                ->color('primary'),

            TextColumn::make('pejantan.kode_ternak')
                ->label('Pejantan')
                ->default('-')
                ->toggleable(),

            TextColumn::make('tanggal_kawin')
                ->date('d M Y')
                ->sortable(),

            TextColumn::make('perkiraan_lahir')
                ->date('d M Y')
                ->toggleable(),

            TextColumn::make('jenis_kawin')
                ->badge()
                ->formatStateUsing(fn ($state) => $state === 'IB' ? 'IB' : 'Alami')
                ->color('info')
                ->toggleable(),

            TextColumn::make('status_siklus')
                ->badge()
                ->color(fn ($state) => match ($state) {
                    'bunting' => 'success',
                    'kawin' => 'warning',
                    'gagal' => 'danger',
                    'melahirkan' => 'info',
                    default => 'gray',
                })
                ->sortable(),

            TextColumn::make('created_at')
                ->dateTime()
                ->toggleable(isToggledHiddenByDefault: true),
        ])

        ->filters([

            SelectFilter::make('betina_id')
                ->relationship('betina', 'kode_ternak')
                ->label('Induk Betina')
                ->searchable(),

            SelectFilter::make('status_siklus')
                ->options([
                    'kosong' => 'Kosong',
                    'kawin' => 'Kawin',
                    'bunting' => 'Bunting',
                    'gagal' => 'Gagal',
                    'melahirkan' => 'Melahirkan',
                ]),

            Filter::make('tanggal_kawin')
                ->form([
                    DatePicker::make('dari'),
                    DatePicker::make('sampai'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['dari'],
                            fn (Builder $query, $date): Builder =>
                                $query->whereDate('tanggal_kawin', '>=', $date),
                        )
                        ->when(
                            $data['sampai'],
                            fn (Builder $query, $date): Builder =>
                                $query->whereDate('tanggal_kawin', '<=', $date),
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

        ->defaultSort('tanggal_kawin', 'desc')
        ->striped();
}
    // ================= PAGES ================= //

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPerkawinans::route('/'),
            'create' => Pages\CreatePerkawinan::route('/create'),
            'edit'   => Pages\EditPerkawinan::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'primary'; 
    }

}
