<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cuentum>
 */
class CuentaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'correo' => $this->faker->unique()->safeEmail(),
            'contrasena' => \Hash::make('password123'),
            'rol' => $this->faker->randomElement(['alumno', 'docente']),
        ];
    }
}
