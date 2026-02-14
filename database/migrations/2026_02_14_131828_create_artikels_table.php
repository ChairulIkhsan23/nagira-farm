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
        Schema::create('artikels', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->foreignId('kategori_id')->nullable()->constrained('kategori_artikels')->onDelete('set null');
            $table->string('judul');
            $table->string('foto')->nullable();
            $table->longText('isi');
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamp('tanggal_publish')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index('judul');
            $table->index('status');
            $table->index('tanggal_publish');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artikels');
    }
};
