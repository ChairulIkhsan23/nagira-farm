<?php

namespace Database\Seeders;

use App\Models\Pengaduan;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PengaduanSeeder extends Seeder
{
    public function run(): void
    {
        $pengaduans = [
            [
                'nama_pengirim' => 'Budi Santoso',
                'email' => 'budi@example.com',
                'kategori' => 'pertanyaan',
                'subjek' => 'Info pembelian bibit kambing',
                'pesan' => 'Apakah saat ini tersedia bibit kambing PE jantan? Saya berminat untuk membeli 5 ekor.',
                'created_at' => Carbon::now()->subDays(5),
            ],
            [
                'nama_pengirim' => 'Siti Aminah',
                'email' => 'siti@example.com',
                'kategori' => 'saran',
                'subjek' => 'Saran untuk website',
                'pesan' => 'Website-nya bagus, mungkin bisa ditambahkan fitur konsultasi online untuk peternak pemula seperti saya.',
                'created_at' => Carbon::now()->subDays(3),
            ],
            [
                'nama_pengirim' => 'Ahmad Fauzi',
                'email' => 'ahmad@example.com',
                'kategori' => 'keluhan',
                'subjek' => 'Keterlambatan respon',
                'pesan' => 'Saya sudah menghubungi via WA 2 hari lalu tapi belum ada respon. Mohon ditindaklanjuti.',
                'created_at' => Carbon::now()->subDays(1),
            ],
        ];

        foreach ($pengaduans as $pengaduan) {
            Pengaduan::create($pengaduan);
        }
    }
}