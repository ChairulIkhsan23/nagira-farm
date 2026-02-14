<?php

namespace App\Filament\Pages;

// Filament Core
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Notifications\Notification;

// Filament Forms
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;

// Laravel
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Pengaturan extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

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
            'nama_lengkap' => $user->nama_lengkap,
            'email'        => $user->email,
            'no_telp'      => $user->no_telp,
            'foto'         => $user->foto,
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
                            ->visibility('public')
                            ->imageEditor()
                            ->maxSize(2048)
                            ->columnSpanFull(),

                        TextInput::make('name')
                            ->label('Username')
                            ->required(),

                        TextInput::make('nama_lengkap')
                            ->label('Nama Lengkap')
                            ->required(),

                        TextInput::make('email')
                            ->email()
                            ->required(),

                        TextInput::make('no_telp')
                            ->label('No. Telepon')
                            ->tel(),

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
                            ->same('password')
                            ->dehydrated(false),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $user = Auth::user();
        $data = $this->form->getState();

        // Handle password manual (override cast)
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        Notification::make()
            ->title('Pengaturan berhasil disimpan')
            ->success()
            ->send();
    }
}
