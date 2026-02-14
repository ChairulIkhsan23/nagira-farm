<?php

namespace Database\Factories;

use App\Models\Perkawinan;
use App\Models\Ternak;
use Illuminate\Database\Eloquent\Factories\Factory;

class PerkawinanFactory extends Factory
{
    protected $model = Perkawinan::class;

    public function definition(): array
    {
        $tanggalKawin = $this->faker->dateTimeBetween('-8 months', '-1 month');
        
        return [
            'betina_id' => Ternak::betina()->inRandomOrder()->first()?->id ?? Ternak::factory()->betina(),
            'pejantan_id' => Ternak::jantan()->inRandomOrder()->first()?->id ?? Ternak::factory()->jantan(),
            'tanggal_kawin' => $tanggalKawin,
            'jenis_kawin' => $this->faker->randomElement(['alami', 'IB']),
            'status_siklus' => $this->faker->randomElement(['kawin', 'bunting', 'gagal', 'melahirkan']),
            'perkiraan_lahir' => date('Y-m-d', strtotime($tanggalKawin->format('Y-m-d') . ' +150 days')),
            'keterangan' => $this->faker->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the perkawinan is bunting.
     */
    public function bunting(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status_siklus' => 'bunting',
            ];
        });
    }
}