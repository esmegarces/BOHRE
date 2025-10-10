<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

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
                CicloEscolarSeeder::class,
                GeneracionSeeder::class,
                EspecialidadesSeeder::class,
                AsignaturasSeeder::class,
                PlanAsignaturaSeeder::class,
            ]);
        });
    }
}
