<?php

namespace Database\Seeders;

use App\Models\Artikel;
use App\Models\KategoriArtikel;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArtikelSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil ID kategori
        $kategoriIds = KategoriArtikel::pluck('id', 'nama_kategori')->toArray();
        
        $artikels = [
            [
                'kategori_nama' => 'Peternakan Modern',
                'judul' => 'Teknologi IoT untuk Monitoring Kandang Kambing Modern',
                'excerpt' => 'Pelajari bagaimana teknologi Internet of Things dapat membantu memantau kondisi kandang dan kesehatan kambing secara real-time.',
                'isi' => '<h2>Pendahuluan</h2><p>Teknologi IoT (Internet of Things) kini semakin banyak diterapkan dalam dunia peternakan modern. Dengan sensor-sensor pintar, peternak dapat memantau kondisi kandang, suhu, kelembaban, bahkan kesehatan ternak secara real-time dari smartphone mereka.</p><h2>Manfaat IoT dalam Peternakan Kambing</h2><p>Beberapa manfaat utama implementasi IoT antara lain: monitoring suhu dan kelembaban otomatis, deteksi dini penyakit, efisiensi pakan, dan penghematan tenaga kerja.</p><h2>Kesimpulan</h2><p>Investasi dalam teknologi IoT untuk peternakan kambing terbukti meningkatkan produktivitas hingga 30% dan menekan angka kematian ternak.</p>',
                'status' => 'published',
                'is_featured' => true,
                'views' => 1250,
                'tanggal_publish' => Carbon::now()->subDays(2),
            ],
            [
                'kategori_nama' => 'Kesehatan Ternak',
                'judul' => 'Panduan Lengkap Vaksinasi untuk Kambing',
                'excerpt' => 'Jadwal vaksinasi yang tepat untuk mencegah berbagai penyakit berbahaya pada kambing.',
                'isi' => '<h2>Pentingnya Vaksinasi</h2><p>Vaksinasi merupakan langkah preventif paling efektif untuk melindungi kambing dari berbagai penyakit mematikan seperti Orf, Brucellosis, dan Septicaemia Epizootica.</p><h2>Jadwal Vaksinasi</h2><ul><li>Umur 0-30 hari: Vaksin anti-Orf</li><li>Umur 2 bulan: Vaksin SE</li><li>Umur 3 bulan: Booster vaksin SE</li><li>Setiap 6 bulan: Vaksin Brucellosis untuk betina</li></ul><h2>Tanda-tanda Kambing Sakit</h2><p>Kenali gejala awal seperti nafsu makan menurun, demam, diare, atau lemas untuk segera mendapatkan penanganan.</p>',
                'status' => 'published',
                'is_featured' => true,
                'views' => 890,
                'tanggal_publish' => Carbon::now()->subDays(5),
            ],
            [
                'kategori_nama' => 'Pakan & Nutrisi',
                'judul' => 'Rahasia Pakan Fermentasi untuk Pertumbuhan Optimal',
                'excerpt' => 'Teknik fermentasi pakan hijauan untuk meningkatkan nutrisi dan daya cerna pada kambing.',
                'isi' => '<h2>Apa itu Pakan Fermentasi?</h2><p>Fermentasi pakan adalah proses pengolahan hijauan menggunakan mikroorganisme baik untuk meningkatkan nilai nutrisi dan daya cerna.</p><h2>Langkah-langkah Fermentasi</h2><ol><li>Potong hijauan (rumput/daun) sepanjang 3-5 cm</li><li>Campur dengan probiotik EM4 dan molases</li><li>Masukkan dalam wadah kedap udara</li><li>Fermentasi selama 7-14 hari</li></ol><h2>Hasil yang Diharapkan</h2><p>Kambing yang diberi pakan fermentasi menunjukkan pertumbuhan 20% lebih cepat dan konversi pakan yang lebih efisien.</p>',
                'status' => 'published',
                'is_featured' => false,
                'views' => 567,
                'tanggal_publish' => Carbon::now()->subDays(7),
            ],
            [
                'kategori_nama' => 'Pembibitan',
                'judul' => 'Cara Memilih Indukan Kambing PE yang Berkualitas',
                'excerpt' => 'Kriteria indukan kambing Peranakan Etawa unggul untuk hasil keturunan terbaik.',
                'isi' => '<h2>Kriteria Indukan Betina Unggul</h2><ul><li>Memiliki silsilah jelas</li><li>Body shape ideal (dada lebar, punggung lurus)</li><li>Produksi susu baik pada generasi sebelumnya</li><li>Sehat dan bebas penyakit reproduksi</li></ul><h2>Kriteria Pejantan Unggul</h2><ul><li>Tumbuh cepat dengan bobot ideal</li><li>Testis simetris dan berkembang sempurna</li><li>Libido tinggi</li><li>Tidak memiliki cacat genetik</li></ul>',
                'status' => 'published',
                'is_featured' => true,
                'views' => 2100,
                'tanggal_publish' => Carbon::now()->subDays(10),
            ],
            [
                'kategori_nama' => 'Berita & Event',
                'judul' => 'Sukses! Kontes Ternak Kambing Nasional 2026',
                'excerpt' => 'Peternak dari 15 provinsi berpartisipasi dalam ajang kontes ternak kambing terbesar tahun ini.',
                'isi' => '<p>Setelah melalui proses seleksi yang ketat, Kontes Ternak Kambing Nasional 2026 telah berhasil dilaksanakan dengan sukses. Acara yang berlangsung selama 3 hari ini diikuti oleh lebih dari 200 ekor kambing terbaik dari 15 provinsi di Indonesia.</p><p>Juara umum diraih oleh Kambing PE jantan bernama "Raja" milik Peternak asal Malang yang berhasil menyabet hadiah utama senilai Rp 50 juta.</p><p>Acara tahun depan direncanakan akan lebih meriah dengan kategori tambahan untuk kambing pedaging dan perah.</p>',
                'status' => 'published',
                'is_featured' => false,
                'views' => 3450,
                'tanggal_publish' => Carbon::now()->subDays(15),
            ],
            [
                'kategori_nama' => 'Tips & Trik',
                'judul' => '5 Cara Mudah Meningkatkan Bobot Kambing Pedaging',
                'excerpt' => 'Tips praktis untuk menggemukkan kambing pedaging dengan cepat dan sehat.',
                'isi' => '<h2>1. Pilih Bibit Unggul</h2><p>Mulai dengan bibit kambing yang memiliki genetik pedaging baik seperti Boer atau persilangannya.</p><h2>2. Pakan Berkualitas</h2><p>Berikan konsentrat dengan protein 16-18% ditambah hijauan berkualitas.</p><h2>3. Jadwal Pakan Teratur</h2><p>Berikan pakan 3x sehari di waktu yang sama untuk membentuk kebiasaan.</p><h2>4. Mineral dan Vitamin</h2><p>Tambahkan mineral block dan vitamin untuk mendukung metabolisme.</p><h2>5. Manajemen Kesehatan</h2><p>Lakukan vaksinasi dan cacingan secara rutin.</p>',
                'status' => 'published',
                'is_featured' => false,
                'views' => 732,
                'tanggal_publish' => Carbon::now()->subDays(20),
            ],
        ];

        foreach ($artikels as $artikel) {
            Artikel::create([
                'slug' => Str::slug($artikel['judul']),
                'kategori_id' => $kategoriIds[$artikel['kategori_nama']],
                'judul' => $artikel['judul'],
                'foto' => null, // Tidak ada foto, akan menggunakan placeholder
                'og_image' => null,
                'isi' => $artikel['isi'],
                'excerpt' => $artikel['excerpt'],
                'status' => $artikel['status'],
                'views' => $artikel['views'],
                'is_featured' => $artikel['is_featured'],
                'tanggal_publish' => $artikel['tanggal_publish'],
                'meta_title' => $artikel['judul'] . ' | Peternakan Kambing Modern',
                'meta_description' => substr($artikel['excerpt'], 0, 160),
                'created_at' => $artikel['tanggal_publish'],
                'updated_at' => $artikel['tanggal_publish'],
            ]);
        }
    }
}