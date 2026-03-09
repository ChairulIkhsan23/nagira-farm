<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Pengaturan extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Pengaturan';
    protected static ?string $navigationGroup = 'Pengaturan';
    protected static string $view = 'filament.pages.pengaturan';
    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public function mount(): void
    {
        $user = Auth::user();

        $this->form->fill([
            'name' => $user->name,
            'nama_lengkap' => $user->nama_lengkap ?? $user->name,
            'email' => $user->email,
            'no_telp' => $user->no_telp,
            'foto' => $user->foto,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Akun')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('foto')
                            ->label('Foto Profil')
                            ->image()
                            ->avatar()
                            ->directory('users')
                            ->disk('public')
                            ->imageEditor()
                            ->maxSize(2048)
                            ->columnSpanFull()
                            ->helperText('Upload foto profil Anda (maks. 2MB)'),

                        TextInput::make('name')
                            ->label('Username')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('nama_lengkap')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        TextInput::make('no_telp')
                            ->label('No. Telepon')
                            ->tel()
                            ->maxLength(20)
                            ->helperText('Contoh: 081234567890'),
                    ]),

                Section::make('Keamanan')
                    ->schema([
                        TextInput::make('password')
                            ->label('Password Baru')
                            ->password()
                            ->revealable()
                            ->dehydrated(fn ($state) => filled($state))
                            ->helperText('Kosongkan jika tidak ingin mengganti password'),

                        TextInput::make('password_confirmation')
                            ->label('Konfirmasi Password')
                            ->password()
                            ->revealable()
                            ->same('password')
                            ->dehydrated(false)
                            ->visible(fn ($get) => filled($get('password'))),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $user = Auth::user();
        $data = $this->form->getState();

        // Handle password
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        
        // Hapus password_confirmation dari data
        unset($data['password_confirmation']);

        // Handle foto jika ada
        if (isset($data['foto']) && is_string($data['foto'])) {
            // Foto sudah dihandle otomatis oleh FileUpload
        }

        $user->update($data);

        Notification::make()
            ->title('Pengaturan berhasil disimpan')
            ->success()
            ->send();
    }

    // Optional: Tambahkan method ini untuk mendapatkan title halaman
    public function getTitle(): string
    {
        return 'Pengaturan Akun';
    }

    // Optional: Tambahkan method ini untuk mendapatkan heading halaman
    public function getHeading(): string
    {
        return 'Pengaturan Akun';
    }
}