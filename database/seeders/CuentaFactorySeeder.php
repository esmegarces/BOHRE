<?php

namespace Database\Seeders;

use App\Models\Alumno;
use App\Models\Cuentum;
use App\Models\Direccion;
use App\Models\Docente;
use App\Models\Persona;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CuentaFactorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function () {
            // Asegurarse que existan localidades para las direcciones
            // cuenta el numero de registros en la tabla localidades
            if (\App\Models\Localidad::count() === 0) {
                $this->command->warn('⚠️ No hay localidades registradas, crea algunas antes de ejecutar el seeder.');
                return;
            }

            // Generar x numero de cuentas con sus relaciones
            Cuentum::factory(800)
                ->create()
                ->each(function ($cuenta) {
                    // Crear una dirección aleatoria vinculada a una localidad existente
                    $direccion = Direccion::factory()->create();

                    // Crear persona relacionada a cuenta y dirección
                    $persona = Persona::factory()
                        ->for($cuenta, 'cuentum')
                        ->for($direccion, 'direccion')
                        ->create();

                    // Según el rol, crear alumno o docente
                    if ($cuenta->rol === 'alumno') {
                        Alumno::factory()
                            ->for($persona, 'persona')
                            ->create();
                    } else {
                        Docente::factory()
                            ->for($persona, 'persona')
                            ->create();
                    }
                });
        });
    }
}
