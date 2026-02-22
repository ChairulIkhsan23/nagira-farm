<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\JenisTernak;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ternaks', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('kode_ternak')->unique();
            $table->string('nama_ternak')->nullable();
            $table->enum('jenis_ternak', JenisTernak::values());
            $table->enum('kategori', ['regular', 'breeding', 'fattening'])->nullable();
            $table->enum('jenis_kelamin', ['jantan', 'betina']);
            $table->date('tanggal_lahir')->nullable();
            $table->decimal('bobot', 8, 2)->nullable();
            $table->date('tanggal_timbang_terakhir')->nullable();
            $table->string('foto')->nullable();
            $table->enum('status_aktif', ['aktif', 'mati', 'terjual'])->default('aktif');
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index(['kategori', 'status_aktif']);
            $table->index('jenis_ternak');
            $table->index('kategori');
            $table->index('jenis_kelamin');
            $table->index('tanggal_lahir');
            $table->index('status_aktif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ternaks');
    }
};
