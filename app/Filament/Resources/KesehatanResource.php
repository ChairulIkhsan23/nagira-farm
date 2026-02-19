<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KesehatanResource\Pages;
use App\Models\Kesehatan;
use App\Models\Ternak;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

// Form Components
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;

// Table Components
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;

class KesehatanResource extends Resource
{
    protected static ?string $model = Kesehatan::class;

    protected static ?string $navigationIcon = 'heroicon-o-face-smile';
    protected static ?string $navigationGroup = 'Manajemen Ternak';
    protected static ?string $navigationLabel = 'Kesehatan';
    protected static ?string $modelLabel = 'Kesehatan';
    protected static ?string $pluralModelLabel = 'Riwayat Kesehatan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Input Data Kesehatan')
                    ->description('Catat riwayat kesehatan ternak')
                    ->icon('heroicon-o-heart')
                    ->schema([

                        Grid::make(2)->schema([

                            Select::make('ternak_id')
                                ->label('Ternak')
                                ->relationship('ternak', 'kode_ternak')
                                ->searchable()
                                ->preload()
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    $ternak = Ternak::find($state);
                                    $set('ternak_nama_preview', $ternak?->nama_ternak);
                                    $set('ternak_jenis_preview', $ternak?->jenis_ternak);
                                })
                                ->required(),

                            DatePicker::make('tanggal_periksa')
                                ->label('Tanggal Periksa')
                                ->required()
                                ->default(now())
                                ->native(false)
                                ->displayFormat('d M Y'),
                        ]),

                        Section::make('Detail Ternak')
                            ->schema([
                                Placeholder::make('ternak_nama_preview')
                                    ->label('Nama Ternak')
                                    ->content(fn ($get) => $get('ternak_nama_preview') ?? '-'),

                                Placeholder::make('ternak_jenis_preview')
                                    ->label('Jenis Ternak')
                                    ->content(fn ($get) => $get('ternak_jenis_preview') ?? '-'),
                            ])
                            ->columns(2)
                            ->collapsible(),

                        Grid::make(2)->schema([

                            Select::make('kondisi')
                                ->label('Kondisi')
                                ->required()
                                ->options([
                                    'sehat' => 'Sehat',
                                    'sakit' => 'Sakit',
                                    'kritis' => 'Kritis',
                                ])
                                ->native(false),

                            TextInput::make('obat')
                                ->placeholder('Contoh: Vitamin B'),
                        ]),

                        TextInput::make('diagnosa')
                            ->placeholder('Contoh: Infeksi ringan'),

                        Textarea::make('tindakan')
                            ->placeholder('Contoh: Pemberian antibiotik selama 3 hari')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('ternak.kode_ternak')
                    ->label('Kode Ternak')
                    ->searchable()
                    ->weight('bold')
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('ternak.nama_ternak')
                    ->searchable()
                    ->limit(20)
                    ->default('-')
                    ->toggleable(),

                TextColumn::make('kondisi')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->color(fn ($state) => match ($state) {
                        'sehat' => 'success',
                        'sakit' => 'warning',
                        'kritis' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('diagnosa')
                    ->limit(25)
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('obat')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('tanggal_periksa')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([

                SelectFilter::make('ternak_id')
                    ->relationship('ternak', 'kode_ternak')
                    ->label('Ternak')
                    ->searchable(),

                SelectFilter::make('kondisi')
                    ->options([
                        'sehat' => 'Sehat',
                        'sakit' => 'Sakit',
                        'kritis' => 'Kritis',
                    ]),

                Filter::make('tanggal_periksa')
                    ->form([
                        DatePicker::make('dari'),
                        DatePicker::make('sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari'],
                                fn (Builder $query, $date): Builder =>
                                    $query->whereDate('tanggal_periksa', '>=', $date),
                            )
                            ->when(
                                $data['sampai'],
                                fn (Builder $query, $date): Builder =>
                                    $query->whereDate('tanggal_periksa', '<=', $date),
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
            ->defaultSort('tanggal_periksa', 'desc')
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKesehatans::route('/'),
            'create' => Pages\CreateKesehatan::route('/create'),
            'edit' => Pages\EditKesehatan::route('/{record}/edit'),
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
