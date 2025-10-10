<?php

namespace Database\Factories;

use App\Models\Cuentum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CuentumFactory extends Factory
{
    protected $model = Cuentum::class;

    public function definition(): array
    {
        return [
            'correo' => $this->faker->unique()->safeEmail(),
            'contrasena' => \Hash::make('password'), // contraseña por defecto para todas las cuentas
            // 98% alumnos y 2% docentes:
            'rol' => $this->faker->numberBetween(1, 100) <= 98 ? 'alumno' : 'docente',
        ];
    }
}
