<?php

namespace Database\Seeders;

use App\Models\Especialidad;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EspecialidadesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::transaction(function () {
           Especialidad::create(['id' => 1, 'nombre' => 'ALIMENTOS']);
           Especialidad::create(['id' => 2, 'nombre' => 'SALUD']);
           Especialidad::create(['id' => 3, 'nombre' => 'ADMINISTRACIÓN']);
        });
    }
}
