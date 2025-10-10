<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Alumno>
 */
class AlumnoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'nia' => $this->faker->unique()->numerify('A#######'),
            'situacion' => $this->faker->randomElement(['ACTIVO', 'BAJA_TEMPORAL', 'BAJA_DEFINITIVA', 'EGRESADO']),
            'idPersona' => null, // se asigna con ->for()
        ];
    }
}
