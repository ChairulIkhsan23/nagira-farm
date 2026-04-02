<?php

namespace Database\Seeders;

use App\Models\KategoriArtikel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KategoriArtikelSeeder extends Seeder
{
    public function run(): void
    {
        $kategoris = [
            [
                'nama_kategori' => 'Peternakan Modern',
                'meta_title' => 'Peternakan Modern - Tips dan Teknologi Terkini',
                'meta_description' => 'Artikel tentang teknologi dan metode peternakan modern untuk meningkatkan produktivitas ternak kambing.',
            ],
            [
                'nama_kategori' => 'Kesehatan Ternak',
                'meta_title' => 'Kesehatan Ternak Kambing - Panduan Lengkap',
                'meta_description' => 'Panduan kesehatan ternak kambing, pencegahan penyakit, dan perawatan yang tepat.',
            ],
            [
                'nama_kategori' => 'Pakan & Nutrisi',
                'meta_title' => 'Pakan dan Nutrisi Ternak Kambing Terbaik',
                'meta_description' => 'Informasi lengkap tentang jenis pakan, nutrisi, dan manajemen pemberian pakan kambing.',
            ],
            [
                'nama_kategori' => 'Pembibitan',
                'meta_title' => 'Pembibitan Kambing Unggul - Panduan Sukses',
                'meta_description' => 'Cara memilih indukan, teknik perkawinan, dan manajemen pembibitan kambing yang berkualitas.',
            ],
            [
                'nama_kategori' => 'Berita & Event',
                'meta_title' => 'Berita dan Event Peternakan Terbaru',
                'meta_description' => 'Informasi terbaru seputar dunia peternakan kambing, seminar, dan pelatihan.',
            ],
            [
                'nama_kategori' => 'Tips & Trik',
                'meta_title' => 'Tips dan Trik Sukses Beternak Kambing',
                'meta_description' => 'Berbagai tips praktis dan trik sukses dalam beternak kambing untuk pemula hingga profesional.',
            ],
        ];

        foreach ($kategoris as $kategori) {
            KategoriArtikel::create([
                'slug' => Str::slug($kategori['nama_kategori']),
                'nama_kategori' => $kategori['nama_kategori'],
                'meta_title' => $kategori['meta_title'],
                'meta_description' => $kategori['meta_description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}