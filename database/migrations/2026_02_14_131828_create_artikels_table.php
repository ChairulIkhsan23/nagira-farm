<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('artikels', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->foreignId('kategori_id')->nullable()->constrained('kategori_artikels')->onDelete('set null');
            $table->string('judul');
            $table->string('foto')->nullable();
            $table->string('og_image')->nullable();
            $table->longText('isi');
            $table->text('excerpt')->nullable();
            $table->enum('status', ['draft', 'published', 'archive', 'scheduled'])->default('draft');
            $table->unsignedBigInteger('views')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamp('tanggal_publish')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            
            
            $table->index('judul');
            $table->index('status');
            $table->index('tanggal_publish');
            $table->index('is_featured');
            $table->index('kategori_id');
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('artikels');
    }
};
