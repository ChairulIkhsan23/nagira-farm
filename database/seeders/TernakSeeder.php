<?php

namespace Database\Seeders;

use App\Models\Ternak;
use App\Enums\JenisTernak;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TernakSeeder extends Seeder
{
    /**
     * Daftar foto yang tersedia di folder public/storage/ternak/
     */
    protected array $availablePhotos = [
        'kambing.webp',
        // Tambahkan foto lain jika ada
        // 'sapi.webp',
        // 'domba.webp',
        // 'kerbau.webp',
    ];

    /**
     * Daftar nama ternak untuk variasi
     */
    protected array $namaDepan = [
        'Si', 'Mbah', 'Ki', 'Nyi', 'Mas', 'Cak', 'Bang', 'Udin', 'Joko', 'Budi',
        'Siti', 'Mawar', 'Melati', 'Kembar', 'Putra', 'Putri', 'Raja', 'Ratu',
    ];

    protected array $namaBelakang = [
        'Unggul', 'Jantan', 'Betina', 'Super', 'Juara', 'Prima', 'Mandiri',
        'Sehat', 'Kuat', 'Lincah', 'Gemuk', 'Mulus', 'Cepat', 'Tangguh',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data existing (jika diperlukan)
        // Ternak::truncate();

        $this->command->info('Memulai generate data ternak...');

        // Generate data untuk setiap jenis ternak
        foreach (JenisTernak::cases() as $jenis) {
            $this->generateForJenisTernak($jenis);
        }

        // Generate data tambahan random
        $this->generateRandomData(20);

        $this->command->info('Data ternak berhasil digenerate!');
    }

    /**
     * Generate data untuk jenis ternak tertentu
     */
    private function generateForJenisTernak(JenisTernak $jenis): void
    {
        $jumlah = match ($jenis) {
            JenisTernak::KAMBING_KACANG => rand(8, 15),
            JenisTernak::KAMBING_JAWARANDU => rand(5, 10),
            JenisTernak::KAMBING_ETAWA => rand(10, 20),
            JenisTernak::KAMBING_PERANAKAN_ETAWA => rand(5, 12),
            JenisTernak::KAMBING_BOER => rand(3, 8),
            JenisTernak::KAMBING_SAANEN => rand(4, 7),
            JenisTernak::KAMBING_ALPINE => rand(2, 5),
            JenisTernak::KAMBING_TOGGENBURG => rand(2, 4),
            JenisTernak::KAMBING_ANGLO_NUBIAN => rand(3, 6),
            JenisTernak::KAMBING_KIKO => rand(2, 5),
            JenisTernak::KAMBING_MYOTONIC => rand(1, 3),
            JenisTernak::KAMBING_LA_MANCHA => rand(2, 4),
            JenisTernak::KAMBING_OBERHASLI => rand(2, 4),
            default => rand(3, 10),
        };

        $this->command->info("Generate {$jumlah} data untuk {$jenis->value}...");

        for ($i = 1; $i <= $jumlah; $i++) {
            $this->createTernak($jenis, $i);
        }
    }

    /**
     * Generate data random tambahan
     */
    private function generateRandomData(int $jumlah): void
    {
        $this->command->info("Generate {$jumlah} data random tambahan...");

        for ($i = 1; $i <= $jumlah; $i++) {
            $jenis = JenisTernak::cases()[array_rand(JenisTernak::cases())];
            $this->createTernak($jenis, null, true);
        }
    }

    /**
     * Create data ternak
     */
    private function createTernak(JenisTernak $jenis, ?int $index = null, bool $random = false): void
    {
        // Tentukan jenis kelamin
        $jenisKelamin = rand(0, 1) ? 'Jantan' : 'Betina';
        
        // Tentukan kategori berdasarkan jenis dan random
        $kategori = $this->determineKategori($jenis, $jenisKelamin);
        
        // Generate nama ternak
        $namaTernak = $this->generateNamaTernak($jenis, $jenisKelamin, $index);
        
        // Tentukan tanggal lahir (1-48 bulan yang lalu)
        $umurBulan = rand(3, 48);
        $tanggalLahir = Carbon::now()->subMonths($umurBulan)->subDays(rand(0, 30));
        
        // Tentukan status aktif (80% aktif, 15% terjual, 5% mati)
        $statusRand = rand(1, 100);
        $statusAktif = match (true) {
            $statusRand <= 80 => 'Aktif',
            $statusRand <= 95 => 'Terjual',
            default => 'Mati',
        };

        // Siapkan data ternak
        $data = [
            'nama_ternak' => $namaTernak,
            'jenis_ternak' => $jenis->value,
            'kategori' => $kategori,
            'jenis_kelamin' => $jenisKelamin,
            'tanggal_lahir' => $tanggalLahir,
            'foto' => $this->getRandomPhoto(),
            'status_aktif' => $statusAktif,
        ];

        // Buat ternak (kode_ternak dan slug akan otomatis tergenerate)
        $ternak = Ternak::create($data);

        // Jika index diberikan dan random false, tampilkan progress
        if ($index && !$random) {
            if ($index % 5 === 0) {
                $this->command->info("  ✓ Created: {$ternak->kode_ternak} - {$ternak->nama_ternak}");
            }
        }
    }

    /**
     * Tentukan kategori berdasarkan jenis dan jenis kelamin
     */
    private function determineKategori(JenisTernak $jenis, string $jenisKelamin): string
    {
        $rand = rand(1, 100);
        
        // Kategorisasi berdasarkan jenis ternak
        $jenisValue = $jenis->value;
        
        // Kambing perah
        if (str_contains($jenisValue, 'Saanen') || 
            str_contains($jenisValue, 'Alpine') || 
            str_contains($jenisValue, 'Toggenburg') || 
            str_contains($jenisValue, 'LaMancha') || 
            str_contains($jenisValue, 'Oberhasli')) {
            
            if ($jenisKelamin === 'Betina') {
                return $rand <= 80 ? 'breeding' : 'regular';
            } else {
                return $rand <= 30 ? 'breeding' : 'fattening';
            }
        }
        
        // Kambing pedaging khusus
        if (str_contains($jenisValue, 'Boer') || 
            str_contains($jenisValue, 'Kiko') || 
            str_contains($jenisValue, 'Myotonic')) {
            
            if ($jenisKelamin === 'Jantan') {
                return $rand <= 70 ? 'fattening' : 'breeding';
            } else {
                return $rand <= 60 ? 'breeding' : 'fattening';
            }
        }
        
        // Kambing lokal
        if ($rand <= 40) {
            return 'regular';
        } elseif ($rand <= 70) {
            return 'breeding';
        } else {
            return 'fattening';
        }
    }

    /**
     * Generate nama ternak
     */
    private function generateNamaTernak(JenisTernak $jenis, string $jenisKelamin, ?int $index = null): string
    {
        $ambilNamaDepan = $this->namaDepan[array_rand($this->namaDepan)];
        $ambilNamaBelakang = $this->namaBelakang[array_rand($this->namaBelakang)];
        
        // Ekstrak kata kunci dari jenis ternak
        $jenisParts = explode(' ', $jenis->value);
        $kataKunci = end($jenisParts); // Ambil kata terakhir
        
        $variasi = [
            "{$ambilNamaDepan} {$kataKunci}",
            "{$kataKunci} {$ambilNamaBelakang}",
            "{$ambilNamaDepan} {$kataKunci} {$ambilNamaBelakang}",
            "{$kataKunci} {$index}",
            "{$jenisKelamin} {$kataKunci}",
        ];
        
        // 70% pakai nama, 30% tanpa nama
        if (rand(1, 100) <= 70) {
            return $variasi[array_rand($variasi)];
        }
        
        return ''; // Kosong, nanti pakai kode ternak saja
    }

    /**
     * Get random photo dari folder yang tersedia
     */
    private function getRandomPhoto(): ?string
    {
        // Jika tidak ada foto, return null
        if (empty($this->availablePhotos)) {
            return null;
        }

        // Pilih foto random dari daftar
        $photo = $this->availablePhotos[array_rand($this->availablePhotos)];
        
        // Return path relatif untuk storage
        return 'ternak/' . $photo;
    }

    /**
     * Tambah foto baru ke daftar available (opsional)
     */
    public function addAvailablePhotos(array $photos): void
    {
        $this->availablePhotos = array_merge($this->availablePhotos, $photos);
    }
}