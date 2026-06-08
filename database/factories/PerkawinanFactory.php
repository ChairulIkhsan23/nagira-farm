<?php

namespace Database\Factories;

use App\Models\Perkawinan;
use App\Models\Ternak;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class PerkawinanFactory extends Factory
{
    protected $model = Perkawinan::class;

    public function definition(): array
    {
        $tanggalKawin = Carbon::now()->subDays($this->faker->numberBetween(1, 270));
        $statusSiklus = $this->faker->randomElement(['kawin', 'bunting', 'gagal', 'melahirkan']);
        
        $perkiraanLahir = match($statusSiklus) {
            'bunting' => $tanggalKawin->copy()->addDays($this->faker->numberBetween(30, 150)),
            'melahirkan' => $tanggalKawin->copy()->addDays($this->faker->numberBetween(140, 160)),
            default => null,
        };
        
        return [
            'betina_id' => null, 
            'pejantan_id' => null, 
            'tanggal_kawin' => $tanggalKawin,
            'jenis_kawin' => $this->faker->randomElement(['alami', 'IB']),
            'status_siklus' => $statusSiklus,
            'perkiraan_lahir' => $perkiraanLahir,
            'keterangan' => $this->faker->optional(0.5)->sentence(),
        ];
    }
    
    public function bunting(): Factory
    {
        return $this->state(fn () => [
            'status_siklus' => 'bunting',
        ]);
    }
    
    public function kawin(): Factory
    {
        return $this->state(fn () => [
            'status_siklus' => 'kawin',
        ]);
    }
    
    public function melahirkan(): Factory
    {
        return $this->state(fn () => [
            'status_siklus' => 'melahirkan',
        ]);
    }
    
    public function gagal(): Factory
    {
        return $this->state(fn () => [
            'status_siklus' => 'gagal',
        ]);
    }
    
    public function alami(): Factory
    {
        return $this->state(fn () => [
            'jenis_kawin' => 'alami',
        ]);
    }
    
    public function ib(): Factory
    {
        return $this->state(fn () => [
            'jenis_kawin' => 'IB',
        ]);
    }
}