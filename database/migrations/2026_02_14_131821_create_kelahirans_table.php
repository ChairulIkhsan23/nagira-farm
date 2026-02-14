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
        Schema::create('kelahirans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('betina_id')->constrained('ternaks')->onDelete('cascade');
            $table->foreignId('perkawinan_id')->nullable()->constrained('perkawinans')->onDelete('cascade');
            $table->date('tanggal_melahirkan');
            $table->unsignedInteger('jumlah_anak_lahir')->default(0);
            $table->unsignedInteger('jumlah_anak_hidup')->default(0);
            $table->unsignedInteger('jumlah_anak_mati')->default(0);
            $table->string('keterangan')->nullable();
            $table->json('detail_anak')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['betina_id', 'tanggal_melahirkan']);
            $table->index('perkawinan_id');
            $table->index('tanggal_melahirkan');
            
            // Foreign key constraint with custom name
            $table->foreign('betina_id', 'kelahirans_betina_id_foreign')
                ->references('id')
                ->on('ternaks')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelahirans');
    }
};
