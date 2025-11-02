<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;


class MainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Throwable
     */
    public function run(): void
    {
        \DB::transaction(function () {
            // ejecutar en orden
            $this->call([
                MunicipioSeeder::class,
                LocalidadSeeder::class,
                GrupoSeeder::class,
                SemestreSeeder::class,
                GeneracionSeeder::class,
                EspecialidadesSeeder::class,
                AsignaturasSeeder::class,
                PlanAsignaturaSeeder::class,
                GrupoSemestreSeeder::class,
            ]);
            // Ejecutar comando artisan clases:generar
            Artisan::call('clases:generar');

            // Opcional: mostrar salida del comando en consola
            $this->command->info(Artisan::output());

            // Finalmente ejecutar el factory o seeder de cuentas
            $this->call([
                CuentaFactorySeeder::class,
            ]);
        });
    }
}
