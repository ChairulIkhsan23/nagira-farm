<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TernakResource\Pages;
use App\Models\Ternak;
use App\Enums\JenisTernak;
use Filament\Actions\DeleteAction;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\FileUpload;

// Form Components
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;

// Table Components
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;

class TernakResource extends Resource
{
    protected static ?string $model = Ternak::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Manajemen Ternak';
    protected static ?string $navigationLabel = 'Ternak';
    protected static ?string $modelLabel = 'Ternak';
    protected static ?string $pluralModelLabel = 'Daftar Ternak';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Dasar Ternak')
                    ->description('Data identitas utama ternak')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('jenis_ternak')
                                    ->required()
                                    ->options(
                                        collect(JenisTernak::cases())
                                            ->mapWithKeys(fn ($case) => [$case->value => $case->value])
                                            ->toArray()
                                    )
                                    ->searchable()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        // Trigger generate kode ternak setelah jenis dipilih
                                        $set('kode_ternak', Ternak::generateKodeTernak($state));
                                    })
                                    ->columnSpan(1),
                                
                                TextInput::make('nama_ternak')
                                    ->label('Nama Ternak (Opsional)')
                                    ->placeholder('Contoh: Slamet Siputih')
                                    ->columnSpan(1),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                TextInput::make('kode_ternak')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->disabled()
                                    ->dehydrated()
                                    ->helperText('Kode ternak otomatis dibuat berdasarkan jenis ternak')
                                    ->columnSpan(1),
                                
                                Hidden::make('slug'),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                Select::make('kategori')
                                    ->options([
                                        'regular' => 'Reguler',
                                        'breeding' => 'Breeding',
                                        'fattening' => 'Fattening',
                                    ])
                                    ->native(false)
                                    ->placeholder('Pilih kategori')
                                    ->default('regular')
                                    ->columnSpan(1)
                                    ->reactive()
                                    ->helperText('Kategori akan mempengaruhi program yang dapat diikuti ternak'),
                                
                                Select::make('jenis_kelamin')
                                    ->required()
                                    ->options([
                                        'jantan' => 'Jantan',
                                        'betina' => 'Betina',
                                    ])
                                    ->native(false)
                                    ->columnSpan(1),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('tanggal_lahir')
                                    ->maxDate(now())
                                    ->native(false)
                                    ->displayFormat('d M Y')
                                    ->columnSpan(1),
                                
                                Select::make('status_aktif')
                                    ->required()
                                    ->options([
                                        'aktif' => 'Aktif',
                                        'terjual' => 'Terjual',
                                        'mati' => 'Mati',
                                    ])
                                    ->default('aktif')
                                    ->native(false)
                                    ->columnSpan(1),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('bobot')
                                    ->label('Bobot Saat Ini (kg)')
                                    ->numeric()
                                    ->step(0.1)
                                    ->minValue(0)
                                    ->suffix('kg')
                                    ->helperText('Bobot ternak saat ini'),
                                
                                DatePicker::make('tanggal_timbang_terakhir')
                                    ->label('Tanggal Timbang Terakhir')
                                    ->maxDate(now())
                                    ->native(false)
                                    ->displayFormat('d M Y')
                                    ->helperText('Tanggal terakhir melakukan penimbangan (otomatis) default ke tanggal lahir') 
                            ]),
                        
                        FileUpload::make('foto')
                            ->label('Foto Ternak')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->directory('ternak/foto')
                            ->visibility('public')
                            ->maxSize(2048) // 2MB
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->helperText('Upload foto ternak. Maksimal 2MB. Format: JPG, PNG, WebP')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto')
                    ->label('Foto')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl(url('/images/default-ternak.png'))
                    ->toggleable(),
                
                TextColumn::make('kode_ternak')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Kode ternak copied')
                    ->weight('bold')
                    ->color('primary')
                    ->sortable(),
                    
                TextColumn::make('nama_ternak')
                    ->searchable()
                    ->limit(20)
                    ->default('-'),
                    
                TextColumn::make('jenis_ternak')
                    ->searchable()
                    ->badge()
                    ->color('success')
                    ->toggleable(),
                    
                TextColumn::make('kategori')
                    ->searchable()
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'regular' => 'Reguler',
                        'breeding' => 'Breeding',
                        'fattening' => 'Fattening',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'regular' => 'gray',
                        'breeding' => 'warning',
                        'fattening' => 'info',
                        default => 'gray',
                    }),
                            
                TextColumn::make('bobot')
                    ->label('Bobot')
                    ->numeric(decimalPlaces: 1)
                    ->suffix(' kg')
                    ->sortable()
                    ->color('success')
                    ->weight('bold'),
                    
                TextColumn::make('jenis_kelamin')
                    ->searchable()
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'jantan' => 'info',
                        'Betina' => 'warning',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'jantan' => 'heroicon-m-arrow-up',
                        'betina' => 'heroicon-m-arrow-down',
                        default => '',
                    }),
                    
                TextColumn::make('tanggal_lahir')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),
                    
                TextColumn::make('umur')
                    ->state(function (Ternak $record): string {
                        return $record->umur_formatted;
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('tanggal_lahir', $direction);
                    })
                    ->toggleable(),
                    
                TextColumn::make('status_aktif')
                    ->searchable()
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'aktif' => 'success',
                        'terjual' => 'warning',
                        'mati' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('jenis_ternak')
                    ->options(
                        collect(JenisTernak::cases())
                            ->mapWithKeys(fn ($case) => [$case->value => $case->value])
                            ->toArray()
                    )
                    ->searchable(),
                    
                SelectFilter::make('kategori')
                    ->options([
                        'regular' => 'Reguler',
                        'breeding' => 'Breeding',
                        'fattening' => 'Fattening',
                    ]),
                    
                SelectFilter::make('jenis_kelamin')
                    ->options([
                        'Jantan' => 'Jantan',
                        'Betina' => 'Betina',
                    ]),
                    
                SelectFilter::make('status_aktif')
                    ->options([
                        'Aktif' => 'Aktif',
                        'Terjual' => 'Terjual',
                        'Mati' => 'Mati',
                    ]),
                    
                Filter::make('tanggal_lahir')
                    ->form([
                        DatePicker::make('dari'),
                        DatePicker::make('sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_lahir', '>=', $date),
                            )
                            ->when(
                                $data['sampai'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_lahir', '<=', $date),
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
            'index' => Pages\ListTernaks::route('/'),
            'create' => Pages\CreateTernak::route('/create'),
            'edit' => Pages\EditTernak::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status_aktif', 'Aktif')->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }
}