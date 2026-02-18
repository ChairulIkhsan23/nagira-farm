<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengaduanResource\Pages;
use App\Models\Pengaduan;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Filters\SelectFilter;
use App\Enums\KategoriPengaduan;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResponPengaduanMail;

class PengaduanResource extends Resource
{
    protected static ?string $model = Pengaduan::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationGroup = 'Layanan';
    protected static ?string $navigationLabel = 'Pengaduan';
    protected static ?string $modelLabel = 'Pengaduan';
    protected static ?string $pluralModelLabel = 'Daftar Pengaduan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Form Pengaduan')
                    ->description('Isi form pengaduan di bawah ini')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('nama_pengirim')
                                    ->label('Nama Lengkap')
                                    ->required()
                                    ->placeholder('Masukkan nama lengkap Anda')
                                    ->maxLength(255)
                                    ->columnSpan(1)
    ,

                                TextInput::make('email')
                                    ->label('Alamat Email')
                                    ->email()
                                    ->placeholder('contoh@email.com')
                                    ->maxLength(255)
                                    ->columnSpan(1),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Select::make('kategori')
                                    ->label('Kategori Pengaduan')
                                    ->required()
                                    ->options(KategoriPengaduan::options())
                                    ->searchable()
                                    ->native(false)
                                    ->columnSpan(1)
    ,

                                TextInput::make('subjek')
                                    ->label('Subjek')
                                    ->placeholder('Ringkasan singkat pengaduan')
                                    ->maxLength(255)
                                    ->columnSpan(1)
    ,
                            ]),

                        Textarea::make('pesan')
                            ->label('Isi Pengaduan')
                            ->required()
                            ->placeholder('Tuliskan detail pengaduan Anda di sini...')
                            ->rows(6)
                            ->maxLength(5000)
                            ->helperText('Maksimal 5000 karakter')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('nama_pengirim')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email copied'),

                TextColumn::make('kategori')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'saran' => 'info',
                        'kritik' => 'warning',
                        'keluhan' => 'danger',
                        'pertanyaan' => 'success',
                        'laporan' => 'primary',
                        'informasi' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('subjek')
                    ->label('Subjek')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),

                TextColumn::make('pesan')
                    ->label('Pesan')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        return $column->getState();
                    }),

                TextColumn::make('created_at')
                    ->label('Dikirim')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('kategori')
                    ->options(KategoriPengaduan::options())
                    ->searchable(),
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

                    Action::make('respon')
                        ->label('Respon via Email')
                        ->icon('heroicon-o-envelope')
                        ->color('success')
                        ->modalButton('Kirim Respon') 
                        ->modalCancelActionLabel('Batal') 
                        ->form([
                            Textarea::make('respon')
                                ->label('Isi Respon')
                                ->required()
                                ->rows(6)
                                ->placeholder('Tuliskan respon Anda untuk pengaduan ini...')
                                ->helperText('Respon ini akan dikirim ke email pengirim'),
                        ])
                        ->action(function (array $data, Pengaduan $record): void {
                            Mail::to($record->email)->send(
                                new ResponPengaduanMail($record, $data['respon'])
                            );
                            
                            Notification::make()
                                ->title('Respon terkirim')
                                ->body('Email respon berhasil dikirim ke ' . $record->email)
                                ->success()
                                ->send();
                        })
                        ->visible(fn (Pengaduan $record): bool => !empty($record->email)), 
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
            'index' => Pages\ListPengaduans::route('/'),
            'create' => Pages\CreatePengaduan::route('/create'),
            'edit' => Pages\EditPengaduan::route('/{record}/edit'),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}