<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Docente>
 */
class DocenteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'cedulaProfesional' => $this->faker->unique()->numerify('########'),
            'numeroExpediente' => $this->faker->unique()->numberBetween(1, 9999),
            'idPersona' => null, // se asigna con ->for()
        ];
    }
}
