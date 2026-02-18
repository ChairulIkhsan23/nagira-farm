<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\JenisPakan;
use App\Enums\NamaPakan;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pakans', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('kode_pakan')->unique();

            $table->enum('jenis_pakan', JenisPakan::values());
            $table->enum('nama_pakan', NamaPakan::values());

            $table->decimal('stok', 12, 2); 
            $table->enum('satuan', ['kg', 'gram', 'ton', 'karung', 'sak', 'ikat', 'liter']);
            $table->text('catatan')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index('jenis_pakan');
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
