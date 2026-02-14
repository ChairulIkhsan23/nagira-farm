<?php

namespace Database\Factories;

use App\Models\Ternak;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TernakFactory extends Factory
{
    protected $model = Ternak::class;

    public function definition(): array
    {
        $jenisKelamin = $this->faker->randomElement(['jantan', 'betina']);
        $kode = 'KTG-' . $this->faker->unique()->numberBetween(100, 999);
        
        return [
            'slug' => Str::slug($kode),
            'kode_ternak' => $kode,
            'nama_ternak' => $this->faker->optional(0.7)->firstName(),
            'jenis_ternak' => $this->faker->randomElement([
                'Kambing Kacang',
                'Kambing Jawarandu',
                'Kambing Etawa',
                'Kambing Peranakan Etawa',
                'Kambing Boer',
            ]),
            'kategori' => $this->faker->randomElement(['regular', 'breeding', 'fattening']),
            'jenis_kelamin' => $jenisKelamin,
            'tanggal_lahir' => $this->faker->optional()->dateTimeBetween('-5 years', '-6 months'),
            'foto' => null,
            'status_aktif' => $this->faker->randomElement(['aktif', 'aktif', 'aktif', 'mati', 'terjual']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the ternak is jantan.
     */
    public function jantan(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'jenis_kelamin' => 'jantan',
            ];
        });
    }

    /**
     * Indicate that the ternak is betina.
     */
    public function betina(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'jenis_kelamin' => 'betina',
            ];
        });
    }

    /**
     * Indicate that the ternak is for breeding.
     */
    public function breeding(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'kategori' => 'breeding',
            ];
        });
    }
}