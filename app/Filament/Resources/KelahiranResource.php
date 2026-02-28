<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KelahiranResource\Pages;
use App\Models\Kelahiran;
use App\Models\Perkawinan;
use App\Models\Ternak;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

// Form Components
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;

// Table Components
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Forms\Components\Section as FormSection;
use Filament\Infolists\Components\Section as InfoSection;


class KelahiranResource extends Resource
{
    protected static ?string $model = Kelahiran::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?string $navigationGroup = 'Program Perkawinan';
    protected static ?string $navigationLabel = 'Kelahiran';
    protected static ?string $modelLabel = 'Kelahiran';
    protected static ?string $pluralModelLabel = 'Data Kelahiran';

    // ================= FORM ================= //

    public static function form(Form $form): Form
    {
        return $form->schema([
            FormSection::make('Informasi Kelahiran')
                ->description('Data siklus reproduksi ternak')
                ->icon('heroicon-o-user-plus')
                ->schema([

                    // ================= BETINA ================= //
                    Select::make('betina_id')
                        ->label('Induk Betina')
                        ->relationship(
                            'betina',
                            'kode_ternak',
                            function ($query) {
                                $query
                                    ->where('jenis_kelamin', 'Betina')

                                    // Harus punya perkawinan aktif
                                    ->whereHas('perkawinanSebagaiBetina', function ($q) {
                                        $q->whereIn('status_siklus', ['kawin', 'bunting']);
                                    })

                                    // BELUM PERNAH MELAHIRKAN
                                    ->whereDoesntHave('kelahirans');
                            }
                        )
                        ->searchable()
                        ->preload()
                        ->reactive()
                        ->required()
                        ->afterStateUpdated(function ($state, callable $set) {

                            $betina = Ternak::find($state);
                            if (!$betina) return;

                            $umur = '-';
                            if ($betina->tanggal_lahir) {
                                $diff = Carbon::parse($betina->tanggal_lahir)->diff(now());
                                $umur = "{$diff->y} th {$diff->m} bln";
                            }

                            $set('betina_kode', $betina->kode_ternak);
                            $set('betina_nama', $betina->nama_ternak);
                            $set('betina_umur', $umur);

                            // reset
                            $set('perkawinan_id', null);
                            $set('pejantan_kode', null);
                            $set('pejantan_nama', null);
                            $set('pejantan_umur', null);
                        }),

                    // INFO BETINA
                    FormSection::make('Detail Betina')
                        ->schema([
                            Grid::make(3)->schema([
                                Placeholder::make('betina_kode')->label('Kode')->content(fn($get)=>$get('betina_kode') ?? '-'),
                                Placeholder::make('betina_nama')->label('Nama')->content(fn($get)=>$get('betina_nama') ?? '-'),
                                Placeholder::make('betina_umur')->label('Umur')->content(fn($get)=>$get('betina_umur') ?? '-'),
                            ]),
                        ])
                        ->collapsible(),

                    // ================= PERKAWINAN ================= //
                    Select::make('perkawinan_id')
                        ->label('Data Perkawinan')
                        ->reactive()
                        ->searchable()
                        ->required()
                        ->visible(fn ($get) => filled($get('betina_id')))
                        ->options(fn ($get) => filled($get('betina_id'))
                            ? Perkawinan::with('pejantan')
                                ->where('betina_id', $get('betina_id'))
                                ->whereIn('status_siklus', ['kawin','bunting'])
                                ->orderBy('tanggal_kawin', 'desc')
                                ->get()
                                ->mapWithKeys(fn ($p) => [
                                    $p->id => Carbon::parse($p->tanggal_kawin)->format('d M Y') .
                                        ' | ' . ($p->pejantan?->kode_ternak ?? '-')
                                ])
                            : []
                        )
                        ->afterStateUpdated(function ($state, callable $set) {

                            $kawin = Perkawinan::with('pejantan')->find($state);
                            if (!$kawin) return;

                            if ($kawin->pejantan) {
                                $j = $kawin->pejantan;

                                $umur = '-';
                                if ($j->tanggal_lahir) {
                                    $diff = Carbon::parse($j->tanggal_lahir)->diff(now());
                                    $umur = "{$diff->y} th {$diff->m} bln";
                                }

                                $set('pejantan_kode', $j->kode_ternak);
                                $set('pejantan_nama', $j->nama_ternak);
                                $set('pejantan_umur', $umur);
                            }
                        }),

                    // INFO PEJANTAN
                    FormSection::make('Detail Pejantan')
                        ->schema([
                            Grid::make(3)->schema([
                                Placeholder::make('pejantan_kode')->label('Kode')->content(fn($get)=>$get('pejantan_kode') ?? '-'),
                                Placeholder::make('pejantan_nama')->label('Nama')->content(fn($get)=>$get('pejantan_nama') ?? '-'),
                                Placeholder::make('pejantan_umur')->label('Umur')->content(fn($get)=>$get('pejantan_umur') ?? '-'),
                            ]),
                        ])
                        ->collapsible(),

                    DatePicker::make('tanggal_melahirkan')
                        ->label('Tanggal Melahirkan')
                        ->required(),

                    Textarea::make('keterangan')->columnSpanFull(),

                    // ================= JUMLAH ANAK ================= //
                    Grid::make(3)->schema([
                        TextInput::make('jumlah_anak_hidup')
                            ->numeric()
                            ->default(0)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::syncAnak($get, $set)),

                        TextInput::make('jumlah_anak_mati')
                            ->numeric()
                            ->default(0)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::syncAnak($get, $set)),

                        TextInput::make('jumlah_anak_lahir')
                            ->disabled()
                            ->dehydrated(),
                    ]),

                    // DETAIL ANAK
                    Repeater::make('detail_anak')
                    ->schema([

                        TextInput::make('nama_ternak')
                            ->label('Nama Anak')
                            ->maxLength(100),

                        Select::make('jenis_kelamin')
                            ->options([
                                'jantan' => 'Jantan',
                                'betina' => 'Betina',
                            ])
                            ->required(),

                        Select::make('kategori')
                            ->options([
                                'regular' => 'Reguler',
                                'breeding' => 'Breeding',
                                'fattening' => 'Fattening',
                            ])
                            ->default('regular')
                            ->required(),

                        TextInput::make('berat_lahir')
                            ->numeric()
                            ->suffix(' kg'),

                        Select::make('status_aktif')
                            ->options([
                                'aktif' => 'Aktif',
                                'mati' => 'Mati',
                                'terjual' => 'Terjual',
                            ])
                            ->default('aktif')
                            ->required(),

                    ])
                    ->columns(2)
                    ->reorderable(false)
                    ->dehydrated(),
                ]),
            // hidden state
            TextInput::make('betina_kode')->hidden(),
            TextInput::make('betina_nama')->hidden(),
            TextInput::make('betina_umur')->hidden(),
            TextInput::make('pejantan_kode')->hidden(),
            TextInput::make('pejantan_nama')->hidden(),
            TextInput::make('pejantan_umur')->hidden(),
        ]);
    }

    // ================= SYNC DETAIL ANAK ================= //
    protected static function syncAnak(callable $get, callable $set)
    {
        $hidup = (int) ($get('jumlah_anak_hidup') ?? 0);
        $mati  = (int) ($get('jumlah_anak_mati') ?? 0);
        $total = $hidup + $mati;

        $set('jumlah_anak_lahir', $total);

        $current = $get('detail_anak') ?? [];
        $count = count($current);

        if ($total > $count) {
            for ($i = $count; $i < $total; $i++) {
                $current[] = ['jenis_kelamin' => null, 'berat_lahir' => null];
            }
        } elseif ($total < $count) {
            $current = array_slice($current, 0, $total);
        }

        $set('detail_anak', $current);
    }

    // ================= TABLE ================= //
    public static function table(Table $table): Table
{
    return $table
        ->columns([

            TextColumn::make('betina.kode_ternak')
                ->label('Induk')
                ->searchable()
                ->sortable()
                ->weight('bold')
                ->color('primary'),

            TextColumn::make('perkawinan.pejantan.kode_ternak')
                ->label('Pejantan')
                ->default('-')
                ->toggleable(),

            TextColumn::make('tanggal_melahirkan')
                ->label('Tanggal Lahir')
                ->date('d M Y')
                ->sortable(),

            TextColumn::make('tanggal_sapih')
                ->label('Tanggal Sapih')
                ->date('d M Y')
                ->toggleable(),

            TextColumn::make('status_sapih')
                ->badge()
                ->color(fn ($record) => $record->sisa_sapih_hari > 0 ? 'warning' : 'success')
                ->toggleable(),

            TextColumn::make('jumlah_anak_lahir')
                ->badge()
                ->color('info')
                ->sortable(),

            TextColumn::make('jumlah_anak_hidup')
                ->badge()
                ->color('success')
                ->sortable()
                ->toggleable(),

            TextColumn::make('jumlah_anak_mati')
                ->badge()
                ->color('danger')
                ->sortable()
                ->toggleable(),

            TextColumn::make('mortalitas_rate')
                ->label('Mortalitas %')
                ->suffix('%')
                ->toggleable(isToggledHiddenByDefault: true),

            TextColumn::make('survival_rate')
                ->label('Survival %')
                ->suffix('%')
                ->toggleable(isToggledHiddenByDefault: true),

            TextColumn::make('created_at')
                ->dateTime()
                ->toggleable(isToggledHiddenByDefault: true),
        ])

        // ================= FILTERS =================
        ->filters([

            // FILTER BETINA
            SelectFilter::make('betina_id')
                ->relationship('betina', 'kode_ternak')
                ->label('Induk Betina')
                ->searchable(),

            // FILTER STATUS SAPIH
            SelectFilter::make('status_sapih')
                ->label('Status Sapih')
                ->options([
                    'menyusui' => 'Menyusui',
                    'sapih' => 'Sudah Sapih',
                ])
                ->query(function (Builder $query, array $data) {
                    return match ($data['value'] ?? null) {
                        'menyusui' => $query->whereDate('tanggal_sapih', '>', now()),
                        'sapih' => $query->whereDate('tanggal_sapih', '<=', now()),
                        default => $query,
                    };
                }),

            // FILTER RANGE TANGGAL MELAHIRKAN
            Filter::make('tanggal_melahirkan')
                ->form([
                    DatePicker::make('dari'),
                    DatePicker::make('sampai'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['dari'],
                            fn (Builder $query, $date) =>
                                $query->whereDate('tanggal_melahirkan', '>=', $date)
                        )
                        ->when(
                            $data['sampai'],
                            fn (Builder $query, $date) =>
                                $query->whereDate('tanggal_melahirkan', '<=', $date)
                        );
                }),
        ])

        // ================= ACTIONS =================
        ->actions([
            ActionGroup::make([
                Action::make('detail_anak')
    ->label('Detail Anak')
    ->icon('heroicon-o-eye')
    ->modalHeading('Detail Anak Kelahiran')
    ->modalSubmitAction(false)
    ->modalWidth('4xl')
    ->infolist([

        InfoSection::make('Statistik Kelahiran')
            ->schema([
                TextEntry::make('jumlah_anak_lahir')->label('Total Lahir'),
                TextEntry::make('jumlah_anak_hidup')->label('Hidup'),
                TextEntry::make('jumlah_anak_mati')->label('Mati'),
                TextEntry::make('mortalitas_rate')
                    ->label('Mortalitas')
                    ->suffix('%'),
                TextEntry::make('survival_rate')
                    ->label('Survival')
                    ->suffix('%'),
            ])
            ->columns(3),

        InfoSection::make('Detail Anak')
            ->schema([
                RepeatableEntry::make('detail_anak')
                    ->schema([
                        TextEntry::make('nama_ternak')
                            ->label('Nama'),

                        TextEntry::make('jenis_kelamin')
                            ->badge()
                            ->color(fn ($state) => match ($state) {
                                'jantan' => 'primary',
                                'betina' => 'danger',
                                default => 'gray',
                            }),

                        TextEntry::make('kategori')
                            ->badge(),

                        TextEntry::make('status_aktif')
                            ->badge()
                            ->color(fn ($state) => match ($state) {
                                'aktif' => 'success',
                                'mati' => 'danger',
                                'terjual' => 'warning',
                                default => 'gray',
                            }),

                        TextEntry::make('berat_lahir')
                            ->suffix(' kg'),
                    ])
                    ->columns(2),
            ])
            ->collapsible(),

    ]),
                EditAction::make()->icon('heroicon-o-pencil')->color('primary'),
                DeleteAction::make()->icon('heroicon-o-trash')->color('danger'),
            ]),
        ])

        ->bulkActions([
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ])
        ])

        ->defaultSort('tanggal_melahirkan', 'desc')
        ->striped();
}
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKelahirans::route('/'),
            'create' => Pages\CreateKelahiran::route('/create'),
            'edit' => Pages\EditKelahiran::route('/{record}/edit'),
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
