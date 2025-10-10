<?php

namespace Database\Factories;

use App\Models\Localidad;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Docente>
 */
class DireccionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // Obtiene un idLocalidad existente (aleatorio)
        $localidad = Localidad::inRandomOrder()->first(); // del modelo obtiene una lista en orden aleatorio y toma el primero

        return [
            'numeroCasa' => $this->faker->numberBetween(1, 999),
            'calle' => $this->faker->streetName(),
            'idLocalidad' => $localidad ? $localidad->id : 1, // fallback si no hay localidades
        ];
    }
}
