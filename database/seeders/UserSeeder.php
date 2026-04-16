<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin Utama
        User::create([
            'name' => 'admin',
            'nama_lengkap' => 'Administrator',
            'email' => 'admin@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'),
            'remember_token' => Str::random(10),
            'no_telp' => '081234567890',
            'foto' => null,
        ]);

        // Admin Kedua (opsional)
        User::create([
            'name' => 'chairul',
            'nama_lengkap' => 'Chairul Ikhsan',
            'email' => 'chairul@nagirafarm.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'remember_token' => Str::random(10),
            'no_telp' => '081234567891',
            'foto' => null,
        ]);

        // Super Admin (opsional)
        User::create([
            'name' => 'superadmin',
            'nama_lengkap' => 'Super Administrator',
            'email' => 'superadmin@nagirafarm.com',
            'email_verified_at' => now(),
            'password' => Hash::make('super123'),
            'remember_token' => Str::random(10),
            'no_telp' => '081234567892',
            'foto' => null,
        ]);
    }
}