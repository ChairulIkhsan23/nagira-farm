<?php

namespace Database\Seeders;

use App\Models\KategoriArtikel;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Laravel\Prompts\Key;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            KategoriArtikelSeeder::class,
            ArtikelSeeder::class,
            TernakSeeder::class,
            PengaduanSeeder::class,
            // Seeder lainnya
        ]);
    }
}
