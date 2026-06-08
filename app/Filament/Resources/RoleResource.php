<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasFeaturePermission;
use App\Filament\Resources\RoleResource\Pages;
use App\Models\Role;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class RoleResource extends Resource
{
    use HasFeaturePermission;

    protected static ?string $model = Role::class;
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = 'Manajemen Akses';
    protected static ?string $navigationLabel = 'Role';
    protected static ?string $modelLabel = 'Role';
    protected static ?string $pluralModelLabel = 'Daftar Role';
    protected static ?string $permissionKey = 'roles';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Informasi Role')
                ->schema([
                    TextInput::make('name')
                        ->label('Nama Role')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug((string) $state))),
                    TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    Textarea::make('description')
                        ->label('Deskripsi')
                        ->rows(3),
                ]),
            Section::make('Permission')
                ->schema([
                    CheckboxList::make('permissions')
                        ->relationship('permissions', 'name', fn ($query) => $query->orderBy('group')->orderBy('name'))
                        ->columns(2)
                        ->bulkToggleable()
                        ->searchable(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Role')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('permissions_count')
                    ->label('Jumlah Permission')
                    ->counts('permissions'),
                TextColumn::make('users_count')
                    ->label('Jumlah Pengguna')
                    ->counts('users'),
                IconColumn::make('is_system')
                    ->label('Sistem')
                    ->boolean(),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make()
                        ->hidden(fn (Role $record): bool => $record->is_system || $record->users()->exists()),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
