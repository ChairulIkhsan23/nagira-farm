<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->updateOrCreate([
            'email' => 'admin@gmail.com',
        ], [
            'name' => 'admin',
            'nama_lengkap' => 'Administrator',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'),
            'remember_token' => Str::random(10),
            'no_telp' => '081234567890',
            'foto' => null,
        ]);

        $chairul = User::query()->updateOrCreate([
            'email' => 'chairul@nagirafarm.com',
        ], [
            'name' => 'chairul',
            'nama_lengkap' => 'Chairul Ikhsan',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'remember_token' => Str::random(10),
            'no_telp' => '081234567891',
            'foto' => null,
        ]);

        $superadmin = User::query()->updateOrCreate([
            'email' => 'superadmin@nagirafarm.com',
        ], [
            'name' => 'superadmin',
            'nama_lengkap' => 'Super Administrator',
            'email_verified_at' => now(),
            'password' => Hash::make('super123'),
            'remember_token' => Str::random(10),
            'no_telp' => '081234567892',
            'foto' => null,
        ]);

        $admin->syncRolesBySlug(['admin']);
        $chairul->syncRolesBySlug(['admin']);
        $superadmin->syncRolesBySlug(['superadmin']);
    }
}
