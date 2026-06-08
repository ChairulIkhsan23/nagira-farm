<?php

namespace App\Models;

use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements HasAvatar
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'nama_lengkap',
        'email',
        'google_id',
        'no_telp',
        'foto',
        'email_verified_at',
        'password',

    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole(string $role): bool
    {
        return $this->roles()->where('slug', $role)->exists();
    }

    public function hasPermissionTo(string $permission): bool
    {
        if ($this->hasRole('superadmin')) {
            return true;
        }

        return $this->roles()
            ->whereHas('permissions', fn ($query) => $query->where('slug', $permission))
            ->exists();
    }

    public function syncRolesBySlug(array $roleSlugs): void
    {
        $roleIds = Role::query()
            ->whereIn('slug', $roleSlugs)
            ->pluck('id');

        $this->roles()->sync($roleIds);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->roles()->exists();
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->foto
            ? asset('storage/' . $this->foto)
            : null;
    }
}
