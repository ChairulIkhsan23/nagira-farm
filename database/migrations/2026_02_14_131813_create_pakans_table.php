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
        Schema::create('pakans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ternak_id')->constrained('ternaks')->onDelete('cascade');
            $table->string('jenis_pakan');
            $table->double('jumlah_pakan');
            $table->timestamp('tanggal_pemberian');
            $table->text('catatan')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index(['ternak_id', 'jenis_pakan', 'tanggal_pemberian']);
            $table->index('jenis_pakan');
            $table->index('tanggal_pemberian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pakans');
    }
};
