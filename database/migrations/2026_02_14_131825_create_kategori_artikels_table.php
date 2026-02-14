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
        Schema::create('kategori_artikels', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('nama_kategori');
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index('nama_kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_artikels');
    }
};
