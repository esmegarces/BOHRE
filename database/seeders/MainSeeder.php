<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
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
            ]);
        });
    }
}
