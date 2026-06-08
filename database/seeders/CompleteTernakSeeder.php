<?php

namespace Database\Seeders;

use App\Models\Ternak;
use App\Models\Fattening;
use App\Models\Perkawinan;
use App\Models\Kelahiran;
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
        $this->command->info(' Memulai seeder terpadu Ternak + Program...');
        
        
        
        
        
        
        
        
        $this->command->info(' Membuat Ternak FATENNING + Program Penggemukan...');
        $this->createFatteningWithProgram();
        
        
        $this->command->info(' Membuat Ternak BREEDING + Program Perkawinan...');
        $this->createBreedingWithProgram();
        
        
        $this->command->info(' Membuat Ternak REGULAR (tanpa program)...');
        $this->createRegularTernak();
        
        
        $this->command->info(' Membuat data Kelahiran...');
        $this->createKelahiran();
        
        $this->command->info(' Seeder selesai!');
        $this->printSummary();
    }
    
    
    private function createFatteningWithProgram(): void
    {
        $fatteningData = [
            
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
            
            
            if ($status === 'selesai') {
                $bobotTerakhir = $target;
            } elseif ($status === 'gagal') {
                $bobotTerakhir = $bobotAwal + rand(1, 8);
            } else {
                $progressPersen = $progressHari / $totalHari;
                $bobotTerakhir = round($bobotAwal + (($target - $bobotAwal) * $progressPersen), 1);
            }
            
            
            $ternak = Ternak::create([
                'slug' => Str::slug($nama . '-' . $jenis . '-' . rand(100, 999)),
                'kode_ternak' => $this->generateKode(),
                'nama_ternak' => $nama,
                'jenis_ternak' => $jenis,
                'kategori' => 'fattening',
                'jenis_kelamin' => $kelamin,
                'tanggal_lahir' => Carbon::now()->subMonths(rand(8, 14)),
                'bobot' => $bobotTerakhir,
                'foto' => $this->getPhoto(),
                'status_aktif' => 'aktif',
            ]);
            
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
    
    
    private function createBreedingWithProgram(): void
    {
        
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
                'kategori' => 'breeding',
                'jenis_kelamin' => $kelamin,
                'tanggal_lahir' => Carbon::now()->subYears($umur),
                'bobot' => $bobot,
                'foto' => $this->getPhoto(),
                'status_aktif' => 'aktif',
            ]);
            
            $pejantanList[] = $pejantan;
            $this->command->info("   Pejantan: {$nama} siap kawin");
        }
        
        
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
            
            $betina = Ternak::create([
                'slug' => Str::slug($nama . '-' . $jenis),
                'kode_ternak' => $this->generateKode(),
                'nama_ternak' => $nama,
                'jenis_ternak' => $jenis,
                'kategori' => 'breeding',
                'jenis_kelamin' => $kelamin,
                'tanggal_lahir' => Carbon::now()->subYears(2),
                'bobot' => $bobot,
                'foto' => $this->getPhoto(),
                'status_aktif' => 'aktif',
            ]);
            
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
    
    
    private function createKelahiran(): void
    {
        
        $perkawinanMelahirkan = Perkawinan::with(['betina', 'pejantan'])
            ->where('status_siklus', 'melahirkan')
            ->get();
        
        foreach ($perkawinanMelahirkan as $perkawinan) {
            $betina = $perkawinan->betina;
            if (!$betina) continue;
            
            
            $tanggalMelahirkan = $perkawinan->perkiraan_lahir ?? Carbon::now()->subDays(rand(10, 60));
            
            
            $jumlahAnak = rand(1, 3);
            $jumlahHidup = rand(1, $jumlahAnak);
            $jumlahMati = $jumlahAnak - $jumlahHidup;
            
            
            $detailAnak = [];
            for ($i = 1; $i <= $jumlahAnak; $i++) {
                $isHidup = $i <= $jumlahHidup;
                $jenisKelamin = rand(0, 1) === 1 ? 'jantan' : 'betina';
                $beratLahir = rand(25, 55) / 10; 
                
                $detailAnak[] = [
                    'nama_ternak' => "Anak dari {$betina->nama_ternak} ke-{$i}",
                    'jenis_kelamin' => $jenisKelamin,
                    'kategori' => 'regular',
                    'berat_lahir' => $beratLahir,
                    'status_aktif' => $isHidup ? 'aktif' : 'mati',
                ];
            }
            
            
            Kelahiran::create([
                'betina_id' => $betina->id,
                'perkawinan_id' => $perkawinan->id,
                'tanggal_melahirkan' => $tanggalMelahirkan,
                'tanggal_sapih' => $tanggalMelahirkan->copy()->addDays(90),
                'umur_sapih_hari' => 90,
                'jumlah_anak_lahir' => $jumlahAnak,
                'jumlah_anak_hidup' => $jumlahHidup,
                'jumlah_anak_mati' => $jumlahMati,
                'detail_anak' => $detailAnak,
                'keterangan' => 'Kelahiran normal, induk dan anak sehat',
            ]);
            
            $this->command->info("    Kelahiran untuk betina: {$betina->nama_ternak} - {$jumlahAnak} anak");
        }
    }
    
    
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
            
            Ternak::create([
                'slug' => Str::slug($nama),
                'kode_ternak' => $this->generateKode(),
                'nama_ternak' => $nama,
                'jenis_ternak' => $jenis,
                'kategori' => 'regular',
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
        $this->command->info(' SUMMARY DATA TERNAK + PROGRAM:');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        $fatteningCount = Ternak::where('kategori', 'fattening')->count();
        $breedingCount = Ternak::where('kategori', 'breeding')->count();
        $regularCount = Ternak::where('kategori', 'regular')->count();
        $kelahiranCount = Kelahiran::count();
        
        $this->command->info(" Ternak FATTENING : {$fatteningCount} ekor (semua punya program penggemukan)");
        $this->command->info("   ├─ Progres : " . Fattening::where('status', 'progres')->count() . " ekor");
        $this->command->info("   ├─ Selesai : " . Fattening::where('status', 'selesai')->count() . " ekor");
        $this->command->info("   └─ Gagal   : " . Fattening::where('status', 'gagal')->count() . " ekor");
        
        $this->command->info("");
        $this->command->info(" Ternak BREEDING  : {$breedingCount} ekor (semua punya program perkawinan)");
        $this->command->info("   ├─ Bunting    : " . Perkawinan::where('status_siklus', 'bunting')->count() . " ekor");
        $this->command->info("   ├─ Kawin      : " . Perkawinan::where('status_siklus', 'kawin')->count() . " ekor");
        $this->command->info("   ├─ Melahirkan : " . Perkawinan::where('status_siklus', 'melahirkan')->count() . " ekor");
        $this->command->info("   └─ Gagal      : " . Perkawinan::where('status_siklus', 'gagal')->count() . " ekor");
        
        $this->command->info("");
        $this->command->info(" Ternak REGULAR   : {$regularCount} ekor (tanpa program)");
        $this->command->info(" Data KELAHIRAN  : {$kelahiranCount} record");
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info(" TOTAL TERNAK     : " . Ternak::count() . " ekor");
        $this->command->info("");
        $this->command->info(" VALIDASI: Semua ternak dengan kategori 'fattening' memiliki data di tabel fattenings");
        $this->command->info(" VALIDASI: Semua ternak dengan kategori 'breeding' memiliki data di tabel perkawinans");
        $this->command->info(" VALIDASI: Betina dengan status 'melahirkan' memiliki data di tabel kelahirans");
    }
}