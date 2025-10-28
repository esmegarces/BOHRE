<?php

namespace Database\Seeders;

use App\Models\Asignatura;
use App\Models\Clase;
use App\Models\GrupoSemestre;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function () {
            $gruposSemestres = GrupoSemestre::all();

            // por cada grupo_semestre
            foreach ($gruposSemestres as $grupoSemestre) {
                $asignaturaForSemestre = Asignatura::join('plan_asignatura as pa', 'pa.idAsignatura', '=', 'asignatura.id')
                    ->where('idSemestre', $grupoSemestre->idSemestre)
                    ->select('asignatura.*')
                    ->get();

                // por cada asignatura del semestre
                foreach ($asignaturaForSemestre as $asignatura) {
                    // crear una clase
                    Clase::firstOrCreate([
                        'idGrupoSemestre' => $grupoSemestre->id,
                        'idAsignatura' => $asignatura->id,
                    ]);
                }
            }


        });
    }
}
