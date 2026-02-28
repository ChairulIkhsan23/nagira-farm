<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RiwayatTimbangResource\Pages;
use App\Models\RiwayatTimbang;
use App\Models\Fattening;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;

class RiwayatTimbangResource extends Resource
{
    protected static ?string $model = RiwayatTimbang::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'Program Penggemukan';
    protected static ?string $navigationLabel = 'Riwayat Timbang';
    protected static ?string $modelLabel = 'Riwayat Timbang';
    protected static ?string $pluralModelLabel = 'Daftar Riwayat Timbang';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('ternak_id')
                    ->label('Pilih Ternak')
                    ->required()
                    ->relationship(
                        name: 'ternak',
                        titleAttribute: 'kode_ternak',
                        modifyQueryUsing: function (Builder $query, $record) {
                            return $query->where('status_aktif', true)
                                ->where(function ($q) use ($record) {
                                    // Ternak yang punya program progres
                                    $q->whereHas('fattening', function ($subQ) {
                                        $subQ->where('status', 'progres');
                                    });
                                    
                                    // Include ternak yang lagi diedit
                                    if ($record && $record->ternak_id) {
                                        $q->orWhere('id', $record->ternak_id);
                                    }
                                });
                        }
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record): string => 
                        "{$record->kode_ternak} - {$record->nama_ternak} ({$record->jenis_ternak})"
                    )
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set) => $set('fattening_id', null)),
                
                Select::make('fattening_id')
                    ->label('Program Fattening (Opsional)')
                    ->relationship(
                        name: 'fattening',
                        titleAttribute: 'id',
                        modifyQueryUsing: function (Builder $query, callable $get, $record) {
                            $ternakId = $get('ternak_id');
                            
                            // Base query
                            $query->where('status', 'progres');
                            
                            // Filter by ternak
                            if ($ternakId) {
                                $query->where('ternak_id', $ternakId);
                            }
                            
                            // Include program yang lagi diedit
                            if ($record && $record->fattening_id) {
                                $query->orWhere('id', $record->fattening_id);
                            }
                        }
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => 
                        "Program #{$record->formatted_id} - Target: {$record->target_bobot} kg (Progress: {$record->progress_persen}%)"
                    )
                    ->searchable()
                    ->preload()
                    ->nullable(),
                
                TextInput::make('bobot')
                    ->label('Bobot (kg)')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->step(0.1)
                    ->suffix('kg')
                    ->helperText(fn (callable $get) => 
                        $get('fattening_id') ? 'Bobot terakhir akan otomatis terupdate di program fattening' : null
                    ),
                
                DateTimePicker::make('tanggal_timbang')
                    ->label('Tanggal Timbang')
                    ->required()
                    ->default(now())
                    ->native(false)
                    ->displayFormat('d M Y H:i')
                    ->seconds(false),
                
                Textarea::make('catatan')
                    ->label('Catatan')
                    ->placeholder('Masukkan catatan tambahan (opsional)')
                    ->rows(2)
                    ->nullable()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('tanggal_timbang')
                    ->label('Tanggal Timbang')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->searchable(),
                
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
                    ->limit(20)
                    ->toggleable(),
                
                TextColumn::make('bobot')
                    ->label('Bobot')
                    ->numeric(decimalPlaces: 1)
                    ->suffix(' kg')
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),
                
                TextColumn::make('selisih_bobot_formatted')
                    ->label('Δ Bobot')
                    ->badge()
                    ->color(fn ($record) => $record->gain_color)
                    ->toggleable(),
                
                TextColumn::make('adg_formatted')
                    ->label('ADG')
                    ->badge()
                    ->color('info')
                    ->toggleable(),
                
                TextColumn::make('fattening.formatted_id')
                    ->label('Program')
                    ->searchable()
                    ->default('-')
                    ->formatStateUsing(fn ($state) => $state ?? '-')
                    ->url(fn ($record) => $record->fattening_id ? 
                        FatteningResource::getUrl('edit', ['record' => $record->fattening_id]) : null
                    )
                    ->color('secondary')
                    ->toggleable(),
                
                TextColumn::make('catatan')
                    ->label('Catatan')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(),
                
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('ternak_id')
                    ->label('Filter Ternak')
                    ->relationship('ternak', 'kode_ternak')
                    ->searchable()
                    ->preload(),
                
                SelectFilter::make('fattening_id')
                    ->label('Filter Program')
                    ->relationship('fattening', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => 
                        "Program #{$record->formatted_id} - Ternak: {$record->ternak->kode_ternak}"
                    )
                    ->searchable()
                    ->preload(),
                
                Filter::make('tanggal_timbang')
                    ->form([
                        DatePicker::make('dari')
                            ->label('Dari Tanggal'),
                        DatePicker::make('sampai')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_timbang', '>=', $date),
                            )
                            ->when(
                                $data['sampai'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_timbang', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['dari'] ?? null) {
                            $indicators[] = 'Dari: ' . \Carbon\Carbon::parse($data['dari'])->format('d M Y');
                        }
                        if ($data['sampai'] ?? null) {
                            $indicators[] = 'Sampai: ' . \Carbon\Carbon::parse($data['sampai'])->format('d M Y');
                        }
                        return $indicators;
                    }),
                
                Filter::make('positive_gain')
                    ->label('Pertumbuhan Positif')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->whereHas('fattening', function ($q) {
                        // Ini akan difilter di model scope
                    })),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make()
                        ->icon('heroicon-o-pencil')
                        ->color('primary'),
                    
                    DeleteAction::make()
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Riwayat Timbang')
                        ->modalDescription('Apakah Anda yakin ingin menghapus riwayat timbang ini? Bobot terakhir program fattening akan dikembalikan ke bobot sebelumnya.')
                        ->modalSubmitActionLabel('Ya, Hapus'),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Riwayat Timbang')
                        ->modalDescription('Apakah Anda yakin ingin menghapus riwayat timbang yang dipilih? Bobot terakhir program fattening akan dikembalikan ke bobot sebelumnya.')
                        ->modalSubmitActionLabel('Ya, Hapus'),
                ]),
            ])
            ->defaultSort('tanggal_timbang', 'desc')
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
            'index' => Pages\ListRiwayatTimbangs::route('/'),
            'create' => Pages\CreateRiwayatTimbang::route('/create'),
            'edit' => Pages\EditRiwayatTimbang::route('/{record}/edit'),
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