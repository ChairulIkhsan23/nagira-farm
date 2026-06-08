<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('kesehatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ternak_id')->constrained('ternaks')->onDelete('cascade');
            $table->enum('kondisi', ['sehat', 'sakit', 'kritis']);
            $table->string('diagnosa')->nullable();
            $table->text('tindakan')->nullable();
            $table->string('obat')->nullable();
            $table->timestamp('tanggal_periksa')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            
            $table->index(['ternak_id', 'tanggal_periksa']);
            $table->index('kondisi');
            $table->index('tanggal_periksa');
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('kesehatans');
    }
};
