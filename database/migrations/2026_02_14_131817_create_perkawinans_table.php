<?php

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
        Schema::create('perkawinans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('betina_id')->constrained('ternaks')->onDelete('cascade');
            $table->foreignId('pejantan_id')->nullable()->constrained('ternaks')->onDelete('set null');
            $table->date('tanggal_kawin')->nullable();
            $table->enum('jenis_kawin', ['alami', 'IB']);
            $table->enum('status_siklus', ['kosong', 'kawin', 'bunting', 'gagal', 'melahirkan'])->default('kawin');
            $table->date('perkiraan_lahir')->nullable();
            $table->string('keterangan')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['betina_id', 'tanggal_kawin']);
            $table->index(['status_siklus', 'tanggal_kawin']);
            $table->index('tanggal_kawin');
            $table->index('status_siklus');
            $table->index('perkiraan_lahir');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perkawinans');
    }
};
