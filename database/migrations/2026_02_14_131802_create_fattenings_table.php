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
        Schema::create('fattenings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ternak_id')->constrained('ternaks')->onDelete('cascade')->unique();
            $table->double('bobot_awal')->nullable();
            $table->double('bobot_terakhir')->nullable();
            $table->double('target_bobot')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_target_selesai')->nullable();
            $table->enum('status', ['progres', 'selesai', 'gagal'])->default('progres');
            $table->text('keterangan');
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index(['status', 'tanggal_target_selesai']);
            $table->index('tanggal_mulai');
            $table->index('tanggal_target_selesai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fattenings');
    }
};
