<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as FakerFactory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Persona>
 */
class PersonaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'nombre' => $this->faker->firstName(),
            'apellidoPaterno' => $this->faker->lastName(),
            'apellidoMaterno' => $this->faker->lastName(),
            'curp' => strtoupper($this->faker->unique()->regexify('[A-Z]{4}[0-9]{6}[A-Z]{6}[0-9]{2}')),
            'telefono' => $this->faker->unique()->numerify('##########'),
            'sexo' => $this->faker->randomElement(['M', 'F']),
            'fechaNacimiento' => $this->faker->date(),
            'nss' => $this->faker->unique()->numerify('###########'),
            'idDireccion' => null, // se rellena con ->for()
            'idCuenta' => null,    // se rellena con ->for()
        ];
    }
}
