<?php

namespace Database\Seeders;

use App\Models\Ternak;
use App\Models\Fattening;
use App\Models\Perkawinan;
use App\Enums\JenisTernak;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TernakFatteningBreedingSeeder extends Seeder
{
    protected array $availablePhotos = [
        'kambing.webp',
    ];

    public function run(): void
    {
        $this->command->info(' Memulai seeder Fattening dan Breeding...');
        
        
        $this->command->info(' Membuat data Fattening...');
        $this->createFatteningData();
        
        
        $this->command->info(' Membuat data Breeding (Perkawinan)...');
        $this->createBreedingData();
        
        
        $this->command->info(' Membuat data Regular...');
        $this->createRegularData();
        
        $this->command->info(' Seeder Fattening dan Breeding selesai!');
        $this->printSummary();
    }
    
    private function createFatteningData(): void
    {
        
        $fatteningProgres = [
            [
                'nama' => 'Si Gemuk',
                'jenis' => 'Kambing Etawa',
                'bobot_awal' => 45,
                'target_bobot' => 70,
                'progress_hari' => 30,
                'sisa_hari' => 60,
            ],
            [
                'nama' => 'Joko Kuat',
                'jenis' => 'Kambing Boer',
                'bobot_awal' => 50,
                'target_bobot' => 80,
                'progress_hari' => 20,
                'sisa_hari' => 70,
            ],
            [
                'nama' => 'Si Badar',
                'jenis' => 'Kambing Jawarandu',
                'bobot_awal' => 38,
                'target_bobot' => 65,
                'progress_hari' => 45,
                'sisa_hari' => 45,
            ],
        ];
        
        foreach ($fatteningProgres as $data) {
            $ternak = Ternak::create([
                'slug' => Str::slug($data['nama'] . '-fattening'),
                'kode_ternak' => $this->generateKode(),
                'nama_ternak' => $data['nama'],
                'jenis_ternak' => $data['jenis'],
                'kategori' => 'fattening',
                'jenis_kelamin' => 'jantan',
                'tanggal_lahir' => Carbon::now()->subMonths(12),
                'bobot' => $data['bobot_awal'],
                'foto' => $this->getPhoto(),
                'status_aktif' => 'aktif',
            ]);
            
            $bobotTerakhir = $data['bobot_awal'] + (($data['target_bobot'] - $data['bobot_awal']) * ($data['progress_hari'] / ($data['progress_hari'] + $data['sisa_hari'])));
            
            Fattening::create([
                'ternak_id' => $ternak->id,
                'bobot_awal' => $data['bobot_awal'],
                'bobot_terakhir' => round($bobotTerakhir, 1),
                'target_bobot' => $data['target_bobot'],
                'tanggal_mulai' => Carbon::now()->subDays($data['progress_hari']),
                'tanggal_target_selesai' => Carbon::now()->addDays($data['sisa_hari']),
                'status' => 'progres',
                'keterangan' => "Program penggemukan target {$data['target_bobot']} kg",
            ]);
        }
        
        
        $ternakSelesai = Ternak::create([
            'slug' => 'budi-super-selesai',
            'kode_ternak' => $this->generateKode(),
            'nama_ternak' => 'Budi Super',
            'jenis_ternak' => 'Kambing Jawarandu',
            'kategori' => 'regular',
            'jenis_kelamin' => 'jantan',
            'tanggal_lahir' => Carbon::now()->subMonths(18),
            'bobot' => 65,
            'foto' => $this->getPhoto(),
            'status_aktif' => 'aktif',
        ]);
        
        Fattening::create([
            'ternak_id' => $ternakSelesai->id,
            'bobot_awal' => 35,
            'bobot_terakhir' => 65,
            'target_bobot' => 60,
            'tanggal_mulai' => Carbon::now()->subMonths(5),
            'tanggal_target_selesai' => Carbon::now()->subDays(10),
            'status' => 'selesai',
            'keterangan' => 'Program penggemukan sukses, target tercapai',
        ]);
        
        
        $ternakGagal = Ternak::create([
            'slug' => 'mbah-gemuk-gagal',
            'kode_ternak' => $this->generateKode(),
            'nama_ternak' => 'Mbah Gemuk',
            'jenis_ternak' => 'Kambing Kacang',
            'kategori' => 'regular',
            'jenis_kelamin' => 'jantan',
            'tanggal_lahir' => Carbon::now()->subMonths(8),
            'bobot' => 28,
            'foto' => $this->getPhoto(),
            'status_aktif' => 'aktif',
        ]);
        
        Fattening::create([
            'ternak_id' => $ternakGagal->id,
            'bobot_awal' => 25,
            'bobot_terakhir' => 28,
            'target_bobot' => 50,
            'tanggal_mulai' => Carbon::now()->subMonths(2),
            'tanggal_target_selesai' => Carbon::now()->subDays(5),
            'status' => 'gagal',
            'keterangan' => 'Program gagal karena ternak sakit',
        ]);
    }
    
    private function createBreedingData(): void
    {
        
        $pejantan1 = Ternak::create([
            'slug' => 'si-jaka-pejantan',
            'kode_ternak' => $this->generateKode(),
            'nama_ternak' => 'Si Jaka',
            'jenis_ternak' => 'Kambing Boer',
            'kategori' => 'breeding',
            'jenis_kelamin' => 'jantan',
            'tanggal_lahir' => Carbon::now()->subYears(3),
            'bobot' => 75,
            'foto' => $this->getPhoto(),
            'status_aktif' => 'aktif',
        ]);
        
        $pejantan2 = Ternak::create([
            'slug' => 'si-raja-pejantan',
            'kode_ternak' => $this->generateKode(),
            'nama_ternak' => 'Si Raja',
            'jenis_ternak' => 'Kambing Etawa',
            'kategori' => 'breeding',
            'jenis_kelamin' => 'jantan',
            'tanggal_lahir' => Carbon::now()->subYears(4),
            'bobot' => 85,
            'foto' => $this->getPhoto(),
            'status_aktif' => 'aktif',
        ]);
        
        
        $breedingData = [
            [
                'nama' => 'Si Mawar',
                'jenis' => 'Kambing Etawa',
                'status' => 'bunting',
                'pejantan' => $pejantan1,
                'tanggal_kawin' => Carbon::now()->subMonths(4),
                'perkiraan_lahir' => Carbon::now()->addMonths(1),
            ],
            [
                'nama' => 'Si Melati',
                'jenis' => 'Kambing Jawarandu',
                'status' => 'kawin',
                'pejantan' => $pejantan1,
                'tanggal_kawin' => Carbon::now()->subDays(5),
                'perkiraan_lahir' => Carbon::now()->addMonths(5),
            ],
            [
                'nama' => 'Si Ros',
                'jenis' => 'Kambing Etawa',
                'status' => 'melahirkan',
                'pejantan' => $pejantan1,
                'tanggal_kawin' => Carbon::now()->subMonths(6),
                'perkiraan_lahir' => Carbon::now()->subDays(5),
            ],
            [
                'nama' => 'Si Anggun',
                'jenis' => 'Kambing Peranakan Etawa',
                'status' => 'bunting',
                'pejantan' => null,
                'tanggal_kawin' => Carbon::now()->subMonths(3),
                'perkiraan_lahir' => Carbon::now()->addMonths(2),
            ],
            [
                'nama' => 'Si Mutiara',
                'jenis' => 'Kambing Boer',
                'status' => 'bunting',
                'pejantan' => $pejantan2,
                'tanggal_kawin' => Carbon::now()->subMonths(2),
                'perkiraan_lahir' => Carbon::now()->addMonths(3),
            ],
        ];
        
        foreach ($breedingData as $data) {
            $betina = Ternak::create([
                'slug' => Str::slug($data['nama']),
                'kode_ternak' => $this->generateKode(),
                'nama_ternak' => $data['nama'],
                'jenis_ternak' => $data['jenis'],
                'kategori' => 'breeding',
                'jenis_kelamin' => 'betina',
                'tanggal_lahir' => Carbon::now()->subYears(2),
                'bobot' => rand(45, 65),
                'foto' => $this->getPhoto(),
                'status_aktif' => 'aktif',
            ]);
            
            Perkawinan::create([
                'betina_id' => $betina->id,
                'pejantan_id' => $data['pejantan']?->id,
                'tanggal_kawin' => $data['tanggal_kawin'],
                'jenis_kawin' => $data['pejantan'] ? 'alami' : 'IB',
                'status_siklus' => $data['status'],
                'perkiraan_lahir' => $data['perkiraan_lahir'],
                'keterangan' => $data['pejantan'] 
                    ? "Perkawinan dengan pejantan {$data['pejantan']->nama_ternak}"
                    : 'Inseminasi buatan dengan semen beku',
            ]);
        }
        
        
        $betinaGagal = Ternak::create([
            'slug' => 'si-kecil-gagal',
            'kode_ternak' => $this->generateKode(),
            'nama_ternak' => 'Si Kecil',
            'jenis_ternak' => 'Kambing Kacang',
            'kategori' => 'regular',
            'jenis_kelamin' => 'betina',
            'tanggal_lahir' => Carbon::now()->subYears(1.2),
            'bobot' => 32,
            'foto' => $this->getPhoto(),
            'status_aktif' => 'aktif',
        ]);
        
        Perkawinan::create([
            'betina_id' => $betinaGagal->id,
            'pejantan_id' => $pejantan1->id,
            'tanggal_kawin' => Carbon::now()->subMonths(1),
            'jenis_kawin' => 'IB',
            'status_siklus' => 'gagal',
            'perkiraan_lahir' => null,
            'keterangan' => 'Inseminasi gagal, akan diulang',
        ]);
    }
    
    private function createRegularData(): void
    {
        
        $regularData = [
            ['nama' => 'Si Putih', 'jenis' => 'Kambing Etawa', 'bobot' => 42],
            ['nama' => 'Si Hitam', 'jenis' => 'Kambing Boer', 'bobot' => 38],
            ['nama' => 'Si Coklat', 'jenis' => 'Kambing Jawarandu', 'bobot' => 35],
        ];
        
        foreach ($regularData as $data) {
            Ternak::create([
                'slug' => Str::slug($data['nama']),
                'kode_ternak' => $this->generateKode(),
                'nama_ternak' => $data['nama'],
                'jenis_ternak' => $data['jenis'],
                'kategori' => 'regular',
                'jenis_kelamin' => rand(0, 1) ? 'jantan' : 'betina',
                'tanggal_lahir' => Carbon::now()->subMonths(rand(6, 24)),
                'bobot' => $data['bobot'],
                'foto' => $this->getPhoto(),
                'status_aktif' => 'aktif',
            ]);
        }
    }
    
    private function generateKode(): string
    {
        do {
            $kode = 'KTG-' . rand(1000, 9999);
        } while (Ternak::where('kode_ternak', $kode)->exists());
        
        return $kode;
    }
    
    private function getPhoto(): ?string
    {
        return 'ternak/kambing.webp';
    }
    
    private function printSummary(): void
    {
        $this->command->info('');
        $this->command->info(' SUMMARY:');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info(" Fattening Progres : " . Fattening::where('status', 'progres')->count() . " ekor");
        $this->command->info(" Fattening Selesai  : " . Fattening::where('status', 'selesai')->count() . " ekor");
        $this->command->info(" Fattening Gagal    : " . Fattening::where('status', 'gagal')->count() . " ekor");
        $this->command->info('');
        $this->command->info(" Breeding Bunting   : " . Perkawinan::where('status_siklus', 'bunting')->count() . " ekor");
        $this->command->info(" Breeding Kawin     : " . Perkawinan::where('status_siklus', 'kawin')->count() . " ekor");
        $this->command->info(" Breeding Melahirkan: " . Perkawinan::where('status_siklus', 'melahirkan')->count() . " ekor");
        $this->command->info(" Breeding Gagal     : " . Perkawinan::where('status_siklus', 'gagal')->count() . " ekor");
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info(" Total Ternak      : " . Ternak::count() . " ekor");
    }
}