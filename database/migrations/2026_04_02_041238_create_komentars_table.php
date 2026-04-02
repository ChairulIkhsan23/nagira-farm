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
        Schema::create('komentars', function (Blueprint $table) {
            $table->id();

            $table->foreignId('artikel_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('nama'); // tanpa login pun bisa
            $table->string('email')->nullable();

            $table->text('isi');

            // Reply
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('komentars')
                ->cascadeOnDelete();

            // Moderasi
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('komentars');
    }
};
