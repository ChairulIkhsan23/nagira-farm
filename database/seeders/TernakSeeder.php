<?php

namespace Database\Seeders;

use App\Models\Ternak;
use App\Enums\JenisTernak;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TernakSeeder extends Seeder
{
    protected array $availablePhotos = [
        'kambing.webp',
    ];

    public function run(): void
    {
        $this->command->info('Generate data ternak...');

        foreach (JenisTernak::cases() as $jenis) {
            $this->generateForJenis($jenis);
        }

        
        $this->generateRandom(20);

        $this->command->info('Selesai ');
    }

    private function generateForJenis(JenisTernak $jenis): void
    {
        $jumlah = rand(5, 10);

        for ($i = 1; $i <= $jumlah; $i++) {
            $this->createTernak($jenis, $i);
        }
    }

    private function generateRandom(int $jumlah): void
    {
        for ($i = 1; $i <= $jumlah; $i++) {
            $jenis = JenisTernak::cases()[array_rand(JenisTernak::cases())];
            $this->createTernak($jenis);
        }
    }

    private function createTernak(JenisTernak $jenis, ?int $index = null): void
    {
        
        $kode = $this->generateKode();

        
        $slug = Str::slug($kode);

        $jenisKelamin = rand(0, 1) ? 'jantan' : 'betina';

        $kategori = $this->determineKategori($jenis, $jenisKelamin);

        $nama = $this->generateNama($jenis, $jenisKelamin, $index);

        $tanggalLahir = Carbon::now()
            ->subMonths(rand(3, 48))
            ->subDays(rand(0, 30));

        $statusAktif = $this->generateStatus();

        $bobot = round(rand(200, 800) / 10, 1);

        Ternak::create([
            'slug' => $slug,
            'kode_ternak' => $kode,
            'nama_ternak' => $nama ?: null,
            'jenis_ternak' => $jenis->value,
            'kategori' => $kategori,
            'jenis_kelamin' => $jenisKelamin,
            'tanggal_lahir' => $tanggalLahir,
            'bobot' => $bobot,
            'foto' => $this->getPhoto(),
            'status_aktif' => $statusAktif,
        ]);
    }

    private function generateKode(): string
    {
        do {
            $kode = 'KTG-' . rand(1000, 9999);
        } while (Ternak::where('kode_ternak', $kode)->exists());

        return $kode;
    }

    private function generateStatus(): string
    {
        $rand = rand(1, 100);

        return match (true) {
            $rand <= 80 => 'aktif',
            $rand <= 95 => 'terjual',
            default => 'mati',
        };
    }

    private function determineKategori(JenisTernak $jenis, string $kelamin): string
    {
        $rand = rand(1, 100);

        if ($kelamin === 'betina') {
            return $rand <= 70 ? 'breeding' : 'regular';
        }

        return $rand <= 60 ? 'fattening' : 'breeding';
    }

    private function generateNama(JenisTernak $jenis, string $kelamin, ?int $index): string
    {
        $namaDepan = ['Si', 'Mbah', 'Ki', 'Joko', 'Budi'];
        $namaBelakang = ['Super', 'Juara', 'Kuat', 'Gemuk'];

        return $namaDepan[array_rand($namaDepan)] . ' ' .
               $namaBelakang[array_rand($namaBelakang)];
    }

    private function getPhoto(): ?string
    {
        return 'ternak/' . $this->availablePhotos[array_rand($this->availablePhotos)];
    }
}