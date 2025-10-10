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
        $faker = FakerFactory::create('es_MX');

        return [
            'nombre' => $faker->firstName(),
            'apellidoPaterno' => $faker->lastName(),
            'apellidoMaterno' => $faker->lastName(),
            'curp' => strtoupper($faker->unique()->regexify('[A-Z]{4}[0-9]{6}[A-Z]{6}[0-9]{2}')),
            'telefono' => $faker->unique()->numerify('##########'),
            'sexo' => $faker->randomElement(['M', 'F']),
            'fechaNacimiento' => $faker->date(),
            'nss' => $faker->unique()->numerify('###########'),
            'idDireccion' => null, // se rellena con ->for()
            'idCuenta' => null,    // se rellena con ->for()
        ];
    }
}
