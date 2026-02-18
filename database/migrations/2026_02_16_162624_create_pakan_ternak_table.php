<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pakan_ternak', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ternak_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('pakan_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('jumlah', 12, 2); // kg dipakai
            $table->date('tanggal');

            $table->timestamps();

            $table->index(['ternak_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pakan_ternak');
    }
};
