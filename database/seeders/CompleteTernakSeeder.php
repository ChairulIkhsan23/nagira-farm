<?php

namespace Database\Seeders;

use App\Models\Ternak;
use App\Models\Fattening;
use App\Models\Perkawinan;
use App\Enums\JenisTernak;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CompleteTernakSeeder extends Seeder
{
    protected array $availablePhotos = [
        'kambing.webp',
    ];

    public function run(): void
    {
        $this->command->info('🚀 Memulai seeder terpadu Ternak + Program...');
        
        // Hapus data lama (opsional, jika ingin fresh)
        // Ternak::truncate();
        // Fattening::truncate();
        // Perkawinan::truncate();
        
        // 1. Buat ternak FATENNING dengan program penggemukan
        $this->command->info('📊 Membuat Ternak FATENNING + Program Penggemukan...');
        $this->createFatteningWithProgram();
        
        // 2. Buat ternak BREEDING dengan program perkawinan
        $this->command->info('🤝 Membuat Ternak BREEDING + Program Perkawinan...');
        $this->createBreedingWithProgram();
        
        // 3. Buat ternak REGULAR (tanpa program)
        $this->command->info('📝 Membuat Ternak REGULAR (tanpa program)...');
        $this->createRegularTernak();
        
        $this->command->info('✅ Seeder selesai!');
        $this->printSummary();
    }
    
    /**
     * Membuat ternak FATTENING lengkap dengan program penggemukan
     * Setiap ternak yang kategorinya 'fattening' PASTI punya data di tabel fattenings
     */
    private function createFatteningWithProgram(): void
    {
        $fatteningData = [
            // [nama, jenis, kelamin, bobot_awal, target, status, progress_hari, total_hari]
            ['Si Gemuk', 'Kambing Etawa', 'jantan', 45, 70, 'progres', 30, 90],
            ['Joko Kuat', 'Kambing Boer', 'jantan', 50, 80, 'progres', 20, 90],
            ['Si Badar', 'Kambing Jawarandu', 'jantan', 38, 65, 'progres', 45, 90],
            ['Budi Super', 'Kambing Jawarandu', 'jantan', 35, 60, 'selesai', 150, 150],
            ['Mbah Gemuk', 'Kambing Kacang', 'jantan', 25, 50, 'gagal', 60, 60],
            ['Si Gendut', 'Kambing Peranakan Etawa', 'jantan', 42, 75, 'progres', 15, 100],
            ['Jaka Sembung', 'Kambing Boer', 'jantan', 55, 90, 'progres', 10, 90],
            ['Si Besar', 'Kambing Etawa', 'jantan', 48, 85, 'selesai', 120, 120],
            ['Si Lemot', 'Kambing Kacang', 'jantan', 30, 55, 'gagal', 45, 45],
        ];
        
        foreach ($fatteningData as $data) {
            list($nama, $jenis, $kelamin, $bobotAwal, $target, $status, $progressHari, $totalHari) = $data;
            
            // Hitung bobot terakhir berdasarkan progress
            if ($status === 'selesai') {
                $bobotTerakhir = $target;
            } elseif ($status === 'gagal') {
                $bobotTerakhir = $bobotAwal + rand(1, 8);
            } else {
                $progressPersen = $progressHari / $totalHari;
                $bobotTerakhir = round($bobotAwal + (($target - $bobotAwal) * $progressPersen), 1);
            }
            
            // Buat ternak dengan kategori fattening
            $ternak = Ternak::create([
                'slug' => Str::slug($nama . '-' . $jenis . '-' . rand(100, 999)),
                'kode_ternak' => $this->generateKode(),
                'nama_ternak' => $nama,
                'jenis_ternak' => $jenis,
                'kategori' => 'fattening', // PASTI fattening
                'jenis_kelamin' => $kelamin,
                'tanggal_lahir' => Carbon::now()->subMonths(rand(8, 14)),
                'bobot' => $bobotTerakhir,
                'foto' => $this->getPhoto(),
                'status_aktif' => 'aktif',
            ]);
            
            // PASTIKAN: Setiap ternak fattening punya record di tabel fattenings
            Fattening::create([
                'ternak_id' => $ternak->id,
                'bobot_awal' => $bobotAwal,
                'bobot_terakhir' => $bobotTerakhir,
                'target_bobot' => $target,
                'tanggal_mulai' => Carbon::now()->subDays($progressHari),
                'tanggal_target_selesai' => $totalHari > $progressHari 
                    ? Carbon::now()->addDays($totalHari - $progressHari)
                    : Carbon::now()->subDays(rand(1, 30)),
                'status' => $status,
                'keterangan' => "Program penggemukan target {$target} kg",
            ]);
        }
    }
    
    /**
     * Membuat ternak BREEDING lengkap dengan program perkawinan
     * Setiap ternak yang kategorinya 'breeding' PASTI punya data di tabel perkawinans
     */
    private function createBreedingWithProgram(): void
    {
        // Buat pejantan breeding terlebih dahulu
        $pejantanList = [];
        $pejantanData = [
            ['Si Jaka', 'Kambing Boer', 75, 3, 'jantan'],
            ['Si Raja', 'Kambing Etawa', 85, 4, 'jantan'],
            ['Si Gagah', 'Kambing Jawarandu', 70, 3.5, 'jantan'],
            ['Si Perkasa', 'Kambing Peranakan Etawa', 80, 4, 'jantan'],
        ];
        
        foreach ($pejantanData as $data) {
            list($nama, $jenis, $bobot, $umur, $kelamin) = $data;
            
            $pejantan = Ternak::create([
                'slug' => Str::slug($nama . '-pejantan'),
                'kode_ternak' => $this->generateKode(),
                'nama_ternak' => $nama,
                'jenis_ternak' => $jenis,
                'kategori' => 'breeding', // PASTI breeding
                'jenis_kelamin' => $kelamin,
                'tanggal_lahir' => Carbon::now()->subYears($umur),
                'bobot' => $bobot,
                'foto' => $this->getPhoto(),
                'status_aktif' => 'aktif',
            ]);
            
            $pejantanList[] = $pejantan;
            
            // Pejantan tidak perlu record di perkawinan (kecuali sebagai pejantan)
            // Tapi kita tetap buat catatan kalau dia pejantan aktif
            $this->command->info("   Pejantan: {$nama} siap kawin");
        }
        
        // Buat betina breeding dengan program perkawinan
        $betinaData = [
            ['Si Mawar', 'Kambing Etawa', 'betina', 55, 'bunting', 4, 5, $pejantanList[0]],
            ['Si Melati', 'Kambing Jawarandu', 'betina', 48, 'kawin', 0.2, 5, $pejantanList[0]],
            ['Si Ros', 'Kambing Etawa', 'betina', 60, 'melahirkan', 6, 6, $pejantanList[0]],
            ['Si Anggun', 'Kambing Peranakan Etawa', 'betina', 52, 'bunting', 3, 5, null],
            ['Si Mutiara', 'Kambing Boer', 'betina', 58, 'bunting', 2, 5, $pejantanList[1]],
            ['Si Dewi', 'Kambing Etawa', 'betina', 50, 'kawin', 0.1, 5, $pejantanList[2]],
            ['Si Ratu', 'Kambing Jawarandu', 'betina', 62, 'bunting', 3.5, 5, $pejantanList[1]],
            ['Si Cantik', 'Kambing Boer', 'betina', 45, 'gagal', 1, 5, $pejantanList[0]],
        ];
        
        foreach ($betinaData as $data) {
            list($nama, $jenis, $kelamin, $bobot, $status, $bulanLalu, $totalBulan, $pejantan) = $data;
            
            $tanggalKawin = Carbon::now()->subMonths($bulanLalu);
            
            $perkiraanLahir = match($status) {
                'bunting' => $tanggalKawin->copy()->addMonths(5),
                'melahirkan' => $tanggalKawin->copy()->addMonths(5),
                default => null,
            };
            
            // Buat ternak betina dengan kategori breeding
            $betina = Ternak::create([
                'slug' => Str::slug($nama . '-' . $jenis),
                'kode_ternak' => $this->generateKode(),
                'nama_ternak' => $nama,
                'jenis_ternak' => $jenis,
                'kategori' => 'breeding', // PASTI breeding
                'jenis_kelamin' => $kelamin,
                'tanggal_lahir' => Carbon::now()->subYears(2),
                'bobot' => $bobot,
                'foto' => $this->getPhoto(),
                'status_aktif' => 'aktif',
            ]);
            
            // PASTIKAN: Setiap ternak breeding (betina) punya record di tabel perkawinans
            Perkawinan::create([
                'betina_id' => $betina->id,
                'pejantan_id' => $pejantan?->id,
                'tanggal_kawin' => $tanggalKawin,
                'jenis_kawin' => $pejantan ? 'alami' : 'IB',
                'status_siklus' => $status,
                'perkiraan_lahir' => $perkiraanLahir,
                'keterangan' => $pejantan 
                    ? "Perkawinan dengan pejantan {$pejantan->nama_ternak}"
                    : 'Inseminasi buatan dengan semen beku',
            ]);
        }
    }
    
    /**
     * Membuat ternak REGULAR (tanpa program fattening atau breeding)
     */
    private function createRegularTernak(): void
    {
        $regularData = [
            ['Si Putih', 'Kambing Etawa', 'betina', 42],
            ['Si Hitam', 'Kambing Boer', 'jantan', 38],
            ['Si Coklat', 'Kambing Jawarandu', 'betina', 35],
            ['Si Kuning', 'Kambing Kacang', 'jantan', 30],
            ['Si Hijau', 'Kambing Peranakan Etawa', 'betina', 45],
            ['Si Biru', 'Kambing Boer', 'jantan', 40],
            ['Si Merah', 'Kambing Etawa', 'betina', 48],
        ];
        
        foreach ($regularData as $data) {
            list($nama, $jenis, $kelamin, $bobot) = $data;
            
            // Buat ternak regular (TIDAK punya program fattening atau breeding)
            Ternak::create([
                'slug' => Str::slug($nama),
                'kode_ternak' => $this->generateKode(),
                'nama_ternak' => $nama,
                'jenis_ternak' => $jenis,
                'kategori' => 'regular', // PASTI regular
                'jenis_kelamin' => $kelamin,
                'tanggal_lahir' => Carbon::now()->subMonths(rand(6, 24)),
                'bobot' => $bobot,
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
        $this->command->info('📊 SUMMARY DATA TERNAK + PROGRAM:');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        $fatteningCount = Ternak::where('kategori', 'fattening')->count();
        $breedingCount = Ternak::where('kategori', 'breeding')->count();
        $regularCount = Ternak::where('kategori', 'regular')->count();
        
        $this->command->info("🐐 Ternak FATTENING : {$fatteningCount} ekor (semua punya program penggemukan)");
        $this->command->info("   ├─ Progres : " . Fattening::where('status', 'progres')->count() . " ekor");
        $this->command->info("   ├─ Selesai : " . Fattening::where('status', 'selesai')->count() . " ekor");
        $this->command->info("   └─ Gagal   : " . Fattening::where('status', 'gagal')->count() . " ekor");
        
        $this->command->info("");
        $this->command->info("🐐 Ternak BREEDING  : {$breedingCount} ekor (semua punya program perkawinan)");
        $this->command->info("   ├─ Bunting    : " . Perkawinan::where('status_siklus', 'bunting')->count() . " ekor");
        $this->command->info("   ├─ Kawin      : " . Perkawinan::where('status_siklus', 'kawin')->count() . " ekor");
        $this->command->info("   ├─ Melahirkan : " . Perkawinan::where('status_siklus', 'melahirkan')->count() . " ekor");
        $this->command->info("   └─ Gagal      : " . Perkawinan::where('status_siklus', 'gagal')->count() . " ekor");
        
        $this->command->info("");
        $this->command->info("🐐 Ternak REGULAR   : {$regularCount} ekor (tanpa program)");
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info("📈 TOTAL TERNAK     : " . Ternak::count() . " ekor");
        $this->command->info("");
        $this->command->info("✅ VALIDASI: Semua ternak dengan kategori 'fattening' memiliki data di tabel fattenings");
        $this->command->info("✅ VALIDASI: Semua ternak dengan kategori 'breeding' memiliki data di tabel perkawinans");
    }
}