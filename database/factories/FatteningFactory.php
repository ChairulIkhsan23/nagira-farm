<?php

namespace Database\Factories;

use App\Models\Fattening;
use App\Models\Ternak;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class FatteningFactory extends Factory
{
    protected $model = Fattening::class;

    public function definition(): array
    {
        $bobotAwal = $this->faker->randomFloat(1, 25, 60);
        $targetBobot = $bobotAwal + $this->faker->randomFloat(1, 15, 40);
        $tanggalMulai = Carbon::now()->subDays($this->faker->numberBetween(10, 90));
        $status = $this->faker->randomElement(['progres', 'selesai', 'gagal']);
        
        $bobotTerakhir = match($status) {
            'selesai' => $targetBobot,
            'gagal' => $this->faker->randomFloat(1, $bobotAwal, $targetBobot - 5),
            default => $this->faker->randomFloat(1, $bobotAwal, $targetBobot),
        };
        
        return [
            'ternak_id' => null, 
            'bobot_awal' => $bobotAwal,
            'bobot_terakhir' => $bobotTerakhir,
            'target_bobot' => $targetBobot,
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_target_selesai' => $tanggalMulai->copy()->addDays($this->faker->numberBetween(60, 120)),
            'status' => $status,
            'keterangan' => $this->faker->optional(0.7)->sentence(),
        ];
    }
    
    public function progres(): Factory
    {
        return $this->state(fn () => [
            'status' => 'progres',
        ]);
    }
    
    public function selesai(): Factory
    {
        return $this->state(fn () => [
            'status' => 'selesai',
        ]);
    }
    
    public function gagal(): Factory
    {
        return $this->state(fn () => [
            'status' => 'gagal',
        ]);
    }
}