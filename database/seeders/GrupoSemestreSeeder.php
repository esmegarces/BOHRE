<?php

namespace Database\Seeders;

use App\Models\Grupo;
use App\Models\GrupoSemestre;
use App\Models\Semestre;
use Illuminate\Database\Seeder;

class GrupoSemestreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::transaction(function () {
            $grupos = Grupo::all();
            $semestres = Semestre::all();

            foreach ($grupos as $grupo) {
                foreach ($semestres as $semestre) {
                    GrupoSemestre::create([
                        'idSemestre' => $semestre->id,
                        'idGrupo' => $grupo->id,
                    ]);
                }
            }
        });
    }
}
