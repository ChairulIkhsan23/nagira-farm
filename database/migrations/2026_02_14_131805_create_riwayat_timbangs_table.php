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
        Schema::create('riwayat_timbangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ternak_id')->constrained('ternaks')->onDelete('cascade');
            $table->double('bobot');
            $table->foreignId('fattening_id')->nullable()->constrained('fattenings')->onDelete('set null');
            $table->timestamp('tanggal_timbang');
            $table->string('catatan')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index(['ternak_id', 'tanggal_timbang']);
            $table->index(['fattening_id', 'tanggal_timbang']);
            $table->index('tanggal_timbang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_timbangs');
    }
};
