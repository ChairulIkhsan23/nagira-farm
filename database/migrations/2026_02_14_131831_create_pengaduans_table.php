<?php

use App\Enums\KategoriPengaduan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengaduans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pengirim');
            $table->string('email')->nullable();
            $table->enum('kategori', KategoriPengaduan::values());
            $table->string('subjek')->nullable();
            $table->text('pesan');
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index('kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaduans');
    }
};
