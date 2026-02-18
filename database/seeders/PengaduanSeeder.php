<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pengaduan;
use App\Enums\KategoriPengaduan;
use Carbon\Carbon;

class PengaduanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data pengaduan dummy dengan menggunakan enum
        $pengaduans = [
            [
                'nama_pengirim' => 'Budi Santoso',
                'email' => 'budi.santoso@gmail.com',
                'kategori' => KategoriPengaduan::KELUHAN->value,
                'subjek' => 'Pakan ternak tidak sesuai pesanan',
                'pesan' => 'Saya memesan pakan jenis konsentrat sebanyak 10 karung, tetapi yang datang adalah pakan hijauan. Mohon segera ditindaklanjuti karena sudah telat 3 hari.',
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'nama_pengirim' => 'Siti Aminah',
                'email' => 'siti.aminah@yahoo.com',
                'kategori' => KategoriPengaduan::SARAN->value,
                'subjek' => 'Penambahan fitur jadwal vaksin',
                'pesan' => 'Saran untuk pengembangan sistem, mungkin bisa ditambahkan fitur pengingat jadwal vaksin ternak. Agar peternak tidak lupa memberikan vaksin tepat waktu.',
                'created_at' => Carbon::now()->subDays(4),
                'updated_at' => Carbon::now()->subDays(4),
            ],
            [
                'nama_pengirim' => 'Ahmad Hidayat',
                'email' => 'ahmad.hidayat@peternak.id',
                'kategori' => KategoriPengaduan::PERTANYAAN->value,
                'subjek' => 'Cara registrasi ternak baru',
                'pesan' => 'Selamat pagi, saya ingin bertanya mengenai cara mendaftarkan ternak baru di sistem ini. Apakah ada panduan tertulis? Terima kasih.',
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(3),
            ],
            [
                'nama_pengirim' => 'Dewi Lestari',
                'email' => 'dewi.lestari@peternak.com',
                'kategori' => KategoriPengaduan::KRITIK->value,
                'subjek' => 'Loading website lambat',
                'pesan' => 'Website sering loading lama terutama saat mengakses menu laporan. Mohon dioptimalkan kembali agar lebih responsif.',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'nama_pengirim' => 'Joko Widodo',
                'email' => 'joko.widodo@gmail.com',
                'kategori' => KategoriPengaduan::LAPORAN->value,
                'subjek' => 'Temuan bug pada form pakan',
                'pesan' => 'Saya menemukan bug di form input pakan, ketika memilih jenis pakan, field satuan tidak otomatis terisi. Mohon segera diperbaiki.',
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'nama_pengirim' => 'Rina Wulandari',
                'email' => null,
                'kategori' => KategoriPengaduan::INFORMASI->value,
                'subjek' => 'Permintaan data statistik ternak',
                'pesan' => 'Saya sedang penelitian tentang peternakan di daerah. Apakah bisa meminta data statistik ternak untuk keperluan akademis?',
                'created_at' => Carbon::now()->subHours(12),
                'updated_at' => Carbon::now()->subHours(12),
            ],
            [
                'nama_pengirim' => 'Hasan Basri',
                'email' => 'hasan.basri@peternak.com',
                'kategori' => KategoriPengaduan::KELUHAN->value,
                'subjek' => 'Harga pakan naik terus',
                'pesan' => 'Keluhan terkait harga pakan yang terus naik setiap bulan. Mohon ada kebijakan untuk menstabilkan harga demi kesejahteraan peternak kecil.',
                'created_at' => Carbon::now()->subHours(6),
                'updated_at' => Carbon::now()->subHours(6),
            ],
            [
                'nama_pengirim' => 'Putri Maharani',
                'email' => 'putri.maharani@yahoo.com',
                'kategori' => KategoriPengaduan::SARAN->value,
                'subjek' => 'Tambah fitur export Excel',
                'pesan' => 'Saran untuk menambahkan fitur export data ke Excel agar memudahkan rekap data bulanan. Terima kasih.',
                'created_at' => Carbon::now()->subHours(3),
                'updated_at' => Carbon::now()->subHours(3),
            ],
            [
                'nama_pengirim' => 'Eko Prasetyo',
                'email' => 'eko.prasetyo@gmail.com',
                'kategori' => KategoriPengaduan::PERTANYAAN->value,
                'subjek' => 'Cara reset password',
                'pesan' => 'Saya lupa password akun dan tidak menerima email reset password. Mohon bantuannya.',
                'created_at' => Carbon::now()->subHours(2),
                'updated_at' => Carbon::now()->subHours(2),
            ],
            [
                'nama_pengirim' => 'Maya Sari',
                'email' => 'maya.sari@peternak.id',
                'kategori' => KategoriPengaduan::LAINNYA->value,
                'subjek' => 'Testimoni penggunaan sistem',
                'pesan' => 'Terima kasih untuk tim pengembang. Sistem ini sangat membantu manajemen peternakan saya. Semoga semakin sukses.',
                'created_at' => Carbon::now()->subMinutes(30),
                'updated_at' => Carbon::now()->subMinutes(30),
            ],
        ];

        // Insert data ke database
        foreach ($pengaduans as $pengaduan) {
            Pengaduan::create($pengaduan);
        }

        // Tampilkan informasi di console
        $this->command->info('✅ Data pengaduan berhasil digenerate!');
        $this->command->table(
            ['No', 'Nama', 'Kategori', 'Subjek'],
            Pengaduan::latest()->take(5)->get()->map(function ($item, $key) {
                return [
                    $key + 1,
                    $item->nama_pengirim,
                    KategoriPengaduan::tryFrom($item->kategori)?->getLabel() ?? $item->kategori,
                    $item->subjek,
                ];
            })
        );
        $this->command->info('Total data: ' . Pengaduan::count() . ' pengaduan');
    }
}