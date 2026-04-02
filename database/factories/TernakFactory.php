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
        $kode = 'KTG-' . $this->faker->unique()->numberBetween(1000, 9999);

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

            'kategori' => $this->faker->randomElement([
                'regular',
                'breeding',
                'fattening'
            ]),

            'jenis_kelamin' => $this->faker->randomElement([
                'jantan',
                'betina'
            ]),

            'tanggal_lahir' => $this->faker->dateTimeBetween('-5 years', '-6 months'),

            'bobot' => $this->faker->randomFloat(1, 20, 80),

            'foto' => 'ternak/kambing.webp',

            'status_aktif' => $this->faker->randomElement([
                'aktif',
                'aktif',
                'aktif',
                'terjual',
                'mati'
            ]),
        ];
    }

    public function jantan(): Factory
    {
        return $this->state(fn () => [
            'jenis_kelamin' => 'jantan',
        ]);
    }

    public function betina(): Factory
    {
        return $this->state(fn () => [
            'jenis_kelamin' => 'betina',
        ]);
    }

    public function breeding(): Factory
    {
        return $this->state(fn () => [
            'kategori' => 'breeding',
        ]);
    }
}