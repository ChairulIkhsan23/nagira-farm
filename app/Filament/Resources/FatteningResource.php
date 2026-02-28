<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FatteningResource\Pages;
use App\Models\Fattening;
use App\Models\Ternak;
use App\Enums\JenisTernak;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;

class FatteningResource extends Resource
{
    protected static ?string $model = Fattening::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up';
    protected static ?string $navigationGroup = 'Program Penggemukan';
    protected static ?string $navigationLabel = 'Program Fattening';
    protected static ?string $modelLabel = 'Program Fattening';
    protected static ?string $pluralModelLabel = 'Program Fattening';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Program Fattening')
                    ->description('Data program penggemukan ternak')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Select::make('ternak_id')
                            ->label('Pilih Ternak')
                            ->required()
                            ->relationship(
                                name: 'ternak',
                                titleAttribute: 'kode_ternak',
                                modifyQueryUsing: function (Builder $query, $record) {
                                    // Di edit, include ternak yang lagi dipilih
                                    return $query->where('status_aktif', true)
                                        ->where(function ($q) use ($record) {
                                            // Ternak yang belum punya program
                                            $q->whereDoesntHave('programFattening', function (Builder $subQ) {
                                                $subQ->where('status', 'progres');
                                            });
                                            
                                            // Include ternak yang lagi diedit
                                            if ($record && $record->ternak_id) {
                                                $q->orWhere('id', $record->ternak_id);
                                            }
                                        });
                                }
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => 
                                "{$record->kode_ternak} - {$record->nama_ternak} ({$record->jenis_ternak})"
                            )
                            ->searchable()
                            ->preload()
                            ->helperText('Hanya menampilkan ternak yang belum memiliki program fattening aktif')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $ternak = Ternak::find($state);
                                    if ($ternak) {
                                        $set('bobot_awal', $ternak->bobot);
                                    }
                                }
                            })
                            ->disabled((fn ($operation) => $operation === 'edit')),
                        
                        Grid::make(3)
                            ->schema([
                                TextInput::make('bobot_awal')
                                    ->label('Bobot Awal (kg)')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.1)
                                    ->suffix('kg')
                                    ->disabled((fn ($operation) => $operation === 'edit')),
                                
                                TextInput::make('target_bobot')
                                    ->label('Target Bobot (kg)')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.1)
                                    ->suffix('kg')
                                    ->gt('bobot_awal')
                                    ->validationMessages([
                                        'gt' => 'Target bobot harus lebih besar dari bobot awal',
                                    ])
                                    ->disabled(fn ($operation) => $operation === 'edit'),
                                
                                TextInput::make('bobot_terakhir')
                                    ->label('Bobot Terakhir (kg)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.1)
                                    ->suffix('kg')
                                    ->default(fn ($get) => $get('bobot_awal'))
                                    ->helperText('Default: bobot awal')
                                    ->disabled(),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('tanggal_mulai')
                                    ->required()
                                    ->default(now())
                                    ->maxDate(now())
                                    ->native(false)
                                    ->displayFormat('d M Y')
                                    ->disabled(fn ($operation) => $operation === 'edit'),
                                
                                DatePicker::make('tanggal_target_selesai')
                                    ->required()
                                    ->after('tanggal_mulai')
                                    ->native(false)
                                    ->displayFormat('d M Y')
                                    ->disabled(fn ($operation) => $operation === 'edit'),
                            ]),
                        
                        Select::make('status')
                            ->required()
                            ->options([
                                'progres' => 'Dalam Progress',
                                'selesai' => 'Selesai',
                                'gagal' => 'Gagal',
                            ])
                            ->default('progres')
                            ->native(false)
                            ->disabled() // Disabled di semua operasi (readonly)
                            ->helperText('Status berubah otomatis. Tidak dapat diubah manual.'),
                        
                        Textarea::make('keterangan')
                            ->label('Keterangan / Catatan')
                            ->placeholder('Masukkan catatan atau keterangan tambahan tentang program fattening ini')
                            ->rows(3)
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('formatted_id')
                    ->label('Kode Program')
                    ->searchable(query: fn (Builder $query, string $search): Builder => 
                        $query->where('id', 'LIKE', "%{$search}%")
                    )
                    ->copyable()
                    ->copyMessage('Kode program copied')
                    ->weight('bold')
                    ->color('primary')
                    ->sortable(),
                
                TextColumn::make('ternak.kode_ternak')
                    ->label('Kode Ternak')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Kode ternak copied'),
                
                TextColumn::make('ternak.nama_ternak')
                    ->label('Nama Ternak')
                    ->searchable()
                    ->default('-')
                    ->limit(20),
                
                TextColumn::make('bobot_awal')
                    ->label('Bobot Awal')
                    ->numeric(decimalPlaces: 1)
                    ->suffix(' kg')
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('bobot_terakhir')
                    ->label('Bobot Terakhir')
                    ->numeric(decimalPlaces: 1)
                    ->suffix(' kg')
                    ->sortable()
                    ->toggleable()
                    ->color(fn ($record) => $record->bobot_terakhir >= $record->target_bobot ? 'success' : 'warning'),
                
                TextColumn::make('target_bobot')
                    ->label('Target')
                    ->numeric(decimalPlaces: 1)
                    ->suffix(' kg')
                    ->sortable()
                    ->toggleable()
                    ->color('info'),
                
                TextColumn::make('progress_persen')
                    ->label('Progress')
                    ->suffix('%')
                    ->badge()
                    ->color(fn ($record) => match (true) {
                        $record->progress_persen >= 100 => 'success',
                        $record->progress_persen >= 50 => 'warning',
                        default => 'danger',
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderByRaw('((bobot_terakhir - bobot_awal) / (target_bobot - bobot_awal) * 100) ' . $direction);
                    }),
                
                TextColumn::make('tanggal_mulai')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('tanggal_target_selesai')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable()
                    ->color(fn ($record) => $record->is_overdue ? 'danger' : 'default'),
                
                TextColumn::make('sisa_hari')
                    ->label('Sisa Hari')
                    ->badge()
                    ->color(fn ($record) => $record->is_overdue ? 'danger' : 'success')
                    ->formatStateUsing(fn ($record) => $record->is_overdue ? 'Terlambat' : $record->sisa_hari . ' hari')
                    ->toggleable(),
                
                TextColumn::make('status')
                    ->searchable()
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'progres' => 'Dalam Progress',
                        'selesai' => 'Selesai',
                        'gagal' => 'Gagal',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'progres' => 'warning',
                        'selesai' => 'success',
                        'gagal' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'progres' => 'heroicon-o-arrow-path',
                        'selesai' => 'heroicon-o-check-circle',
                        'gagal' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'progres' => 'Dalam Progress',
                        'selesai' => 'Selesai',
                        'gagal' => 'Gagal',
                    ]),
                
                SelectFilter::make('jenis_ternak')
                    ->label('Jenis Ternak')
                    ->options(
                        collect(JenisTernak::cases())
                            ->mapWithKeys(fn ($case) => [$case->value => $case->value])
                            ->toArray()
                    )
                    ->searchable()
                    ->query(function (Builder $query, array $data) {
                        if ($data['value']) {
                            $query->whereHas('ternak', function ($q) use ($data) {
                                $q->where('jenis_ternak', $data['value']);
                            });
                        }
                    }),
                
                Filter::make('overdue')
                    ->label('Terlambat')
                    ->query(fn (Builder $query): Builder => $query->overdue())
                    ->toggle(),
                
                Filter::make('tanggal_mulai')
                    ->form([
                        DatePicker::make('dari')
                            ->label('Tanggal Mulai Dari'),
                        DatePicker::make('sampai')
                            ->label('Tanggal Mulai Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_mulai', '>=', $date),
                            )
                            ->when(
                                $data['sampai'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_mulai', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make() 
                        ->icon('heroicon-o-eye')
                        ->color('info'),
                        
                    EditAction::make()
                        ->icon('heroicon-o-pencil')
                        ->color('primary'),

                    DeleteAction::make()
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Data Fattening')
                        ->modalDescription('Apakah Anda yakin ingin menghapus data fattening ini? Tindakan ini tidak dapat dibatalkan.')
                        ->modalSubmitActionLabel('Ya, Hapus')
                        ->after(function ($record) {
                            $ternak = $record->ternak;
                            if ($ternak) {
                                $hasActiveFattening = Fattening::where('ternak_id', $ternak->id)
                                    ->where('status', 'progres')
                                    ->where('id', '!=', $record->id)
                                    ->exists();
                                
                                if (!$hasActiveFattening) {
                                    $ternak->update(['kategori' => 'regular']);
                                }
                            }
                        }),
                ])
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Data Fattening')
                        ->modalDescription('Apakah Anda yakin ingin menghapus data fattening yang dipilih? Tindakan ini tidak dapat dibatalkan.')
                        ->modalSubmitActionLabel('Ya, Hapus'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
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
            'view' => Pages\ViewFattening::route('/{record}'),
            'edit' => Pages\EditFattening::route('/{record}/edit'),
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