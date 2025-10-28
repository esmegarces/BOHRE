<?php

namespace Database\Seeders;

use App\Models\PlanAsignatura;
use Illuminate\Database\Seeder;

class PlanAsignaturaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        \DB::transaction(function () {
            /* primer semestre */
            PlanAsignatura::updateOrCreate(['id' => 1, 'idAsignatura' => 1, 'idSemestre' => 1]);
            PlanAsignatura::updateOrCreate(['id' => 2, 'idAsignatura' => 2, 'idSemestre' => 1]);
            PlanAsignatura::updateOrCreate(['id' => 3, 'idAsignatura' => 3, 'idSemestre' => 1]);
            PlanAsignatura::updateOrCreate(['id' => 4, 'idAsignatura' => 4, 'idSemestre' => 1]);
            PlanAsignatura::updateOrCreate(['id' => 5, 'idAsignatura' => 5, 'idSemestre' => 1]);
            PlanAsignatura::updateOrCreate(['id' => 6, 'idAsignatura' => 6, 'idSemestre' => 1]);
            PlanAsignatura::updateOrCreate(['id' => 7, 'idAsignatura' => 7, 'idSemestre' => 1]);
            PlanAsignatura::updateOrCreate(['id' => 8, 'idAsignatura' => 8, 'idSemestre' => 1]);
            PlanAsignatura::updateOrCreate(['id' => 9, 'idAsignatura' => 9, 'idSemestre' => 1]);
            PlanAsignatura::updateOrCreate(['id' => 10, 'idAsignatura' => 10, 'idSemestre' => 1]);

            /* segundo semestre */
            PlanAsignatura::updateOrCreate(['id' => 11, 'idAsignatura' => 11, 'idSemestre' => 2]);
            PlanAsignatura::updateOrCreate(['id' => 12, 'idAsignatura' => 12, 'idSemestre' => 2]);
            PlanAsignatura::updateOrCreate(['id' => 13, 'idAsignatura' => 13, 'idSemestre' => 2]);
            PlanAsignatura::updateOrCreate(['id' => 14, 'idAsignatura' => 14, 'idSemestre' => 2]);
            PlanAsignatura::updateOrCreate(['id' => 15, 'idAsignatura' => 15, 'idSemestre' => 2]);
            PlanAsignatura::updateOrCreate(['id' => 16, 'idAsignatura' => 16, 'idSemestre' => 2]);
            PlanAsignatura::updateOrCreate(['id' => 17, 'idAsignatura' => 17, 'idSemestre' => 2]);
            PlanAsignatura::updateOrCreate(['id' => 18, 'idAsignatura' => 18, 'idSemestre' => 2]);
            PlanAsignatura::updateOrCreate(['id' => 19, 'idAsignatura' => 19, 'idSemestre' => 2]);
            PlanAsignatura::updateOrCreate(['id' => 20, 'idAsignatura' => 20, 'idSemestre' => 2]);

            /* tercer semestre */
            PlanAsignatura::updateOrCreate(['id' => 21, 'idAsignatura' => 21, 'idSemestre' => 3]);
            PlanAsignatura::updateOrCreate(['id' => 22, 'idAsignatura' => 22, 'idSemestre' => 3]);
            PlanAsignatura::updateOrCreate(['id' => 23, 'idAsignatura' => 23, 'idSemestre' => 3]);
            PlanAsignatura::updateOrCreate(['id' => 24, 'idAsignatura' => 24, 'idSemestre' => 3]);
            PlanAsignatura::updateOrCreate(['id' => 25, 'idAsignatura' => 25, 'idSemestre' => 3]);
            PlanAsignatura::updateOrCreate(['id' => 26, 'idAsignatura' => 26, 'idSemestre' => 3]);
            PlanAsignatura::updateOrCreate(['id' => 27, 'idAsignatura' => 27, 'idSemestre' => 3]);

            /* cuarto semestre */
            PlanAsignatura::updateOrCreate(['id' => 28, 'idAsignatura' => 28, 'idSemestre' => 4]);
            PlanAsignatura::updateOrCreate(['id' => 29, 'idAsignatura' => 29, 'idSemestre' => 4]);
            PlanAsignatura::updateOrCreate(['id' => 31, 'idAsignatura' => 31, 'idSemestre' => 4]);
            PlanAsignatura::updateOrCreate(['id' => 32, 'idAsignatura' => 32, 'idSemestre' => 4]);
            PlanAsignatura::updateOrCreate(['id' => 33, 'idAsignatura' => 33, 'idSemestre' => 4]);
            PlanAsignatura::updateOrCreate(['id' => 34, 'idAsignatura' => 34, 'idSemestre' => 4]);
            PlanAsignatura::updateOrCreate(['id' => 35, 'idAsignatura' => 35, 'idSemestre' => 4]);
            PlanAsignatura::updateOrCreate(['id' => 36, 'idAsignatura' => 36, 'idSemestre' => 4]);
            PlanAsignatura::updateOrCreate(['id' => 37, 'idAsignatura' => 37, 'idSemestre' => 4]);

            /* quinto semestre */
            //PlanAsignatura::updateOrCreate(['id' => 38, 'idAsignatura' => 21, 'idSemestre' => 5]);
            PlanAsignatura::updateOrCreate(['id' => 39, 'idAsignatura' => 39, 'idSemestre' => 5]);
            PlanAsignatura::updateOrCreate(['id' => 40, 'idAsignatura' => 40, 'idSemestre' => 5]);
            PlanAsignatura::updateOrCreate(['id' => 41, 'idAsignatura' => 41, 'idSemestre' => 5]);
            PlanAsignatura::updateOrCreate(['id' => 42, 'idAsignatura' => 42, 'idSemestre' => 5]);
            PlanAsignatura::updateOrCreate(['id' => 43, 'idAsignatura' => 43, 'idSemestre' => 5]);
            PlanAsignatura::updateOrCreate(['id' => 44, 'idAsignatura' => 44, 'idSemestre' => 5]);

            /* sexto semestre */
            PlanAsignatura::updateOrCreate(['id' => 45, 'idAsignatura' => 45, 'idSemestre' => 6]);
            //PlanAsignatura::updateOrCreate(['id' => 46, 'idAsignatura' => 39, 'idSemestre' => 6]);
            PlanAsignatura::updateOrCreate(['id' => 47, 'idAsignatura' => 47, 'idSemestre' => 6]);
            PlanAsignatura::updateOrCreate(['id' => 48, 'idAsignatura' => 48, 'idSemestre' => 6]);
            PlanAsignatura::updateOrCreate(['id' => 49, 'idAsignatura' => 49, 'idSemestre' => 6]);
            PlanAsignatura::updateOrCreate(['id' => 50, 'idAsignatura' => 50, 'idSemestre' => 6]);
            PlanAsignatura::updateOrCreate(['id' => 51, 'idAsignatura' => 51, 'idSemestre' => 6]);

            /* ESPECIAL ALIMNENTOS */
            /* tercer semestre */
            PlanAsignatura::updateOrCreate(['id' => 52, 'idAsignatura' => 52, 'idSemestre' => 3, 'idEspecialidad' => 1]);
            PlanAsignatura::updateOrCreate(['id' => 53, 'idAsignatura' => 53, 'idSemestre' => 3, 'idEspecialidad' => 1]);
            /* cuarto semestre */
            PlanAsignatura::updateOrCreate(['id' => 54, 'idAsignatura' => 54, 'idSemestre' => 4, 'idEspecialidad' => 1]);
            PlanAsignatura::updateOrCreate(['id' => 55, 'idAsignatura' => 55, 'idSemestre' => 4, 'idEspecialidad' => 1]);
            /* quinto semestre */
            PlanAsignatura::updateOrCreate(['id' => 56, 'idAsignatura' => 56, 'idSemestre' => 5, 'idEspecialidad' => 1]);
            PlanAsignatura::updateOrCreate(['id' => 57, 'idAsignatura' => 57, 'idSemestre' => 5, 'idEspecialidad' => 1]);
            PlanAsignatura::updateOrCreate(['id' => 58, 'idAsignatura' => 58, 'idSemestre' => 5, 'idEspecialidad' => 1]);
            /* sexto semestre */
            PlanAsignatura::updateOrCreate(['id' => 59, 'idAsignatura' => 59, 'idSemestre' => 6, 'idEspecialidad' => 1]);
            PlanAsignatura::updateOrCreate(['id' => 60, 'idAsignatura' => 60, 'idSemestre' => 6, 'idEspecialidad' => 1]);
            //PlanAsignatura::updateOrCreate(['id' => 61, 'idAsignatura' => 61, 'idSemestre' => 6, 'idEspecialidad' => 1]);


            /* ESPECIALIDAD SALUD */
            /* tercer semestre */
            PlanAsignatura::updateOrCreate(['id' => 62, 'idAsignatura' => 62, 'idSemestre' => 3, 'idEspecialidad' => 2]);
            PlanAsignatura::updateOrCreate(['id' => 63, 'idAsignatura' => 63, 'idSemestre' => 3, 'idEspecialidad' => 2]);
            /* cuarto semestre */
            PlanAsignatura::updateOrCreate(['id' => 64, 'idAsignatura' => 64, 'idSemestre' => 4, 'idEspecialidad' => 2]);
            PlanAsignatura::updateOrCreate(['id' => 65, 'idAsignatura' => 65, 'idSemestre' => 4, 'idEspecialidad' => 2]);
            /* quinto semestre */
            PlanAsignatura::updateOrCreate(['id' => 66, 'idAsignatura' => 66, 'idSemestre' => 5, 'idEspecialidad' => 2]);
            PlanAsignatura::updateOrCreate(['id' => 67, 'idAsignatura' => 67, 'idSemestre' => 5, 'idEspecialidad' => 2]);
            PlanAsignatura::updateOrCreate(['id' => 68, 'idAsignatura' => 68, 'idSemestre' => 5, 'idEspecialidad' => 2]);
            /* sexto semestre */
            PlanAsignatura::updateOrCreate(['id' => 69, 'idAsignatura' => 69, 'idSemestre' => 6, 'idEspecialidad' => 2]);
            PlanAsignatura::updateOrCreate(['id' => 70, 'idAsignatura' => 70, 'idSemestre' => 6, 'idEspecialidad' => 2]);
            PlanAsignatura::updateOrCreate(['id' => 71, 'idAsignatura' => 71, 'idSemestre' => 6, 'idEspecialidad' => 2]);


            /* ESPECIALIDAD ADMINISTRACION */
            /* tercer semestre */
            PlanAsignatura::updateOrCreate(['id' => 72, 'idAsignatura' => 72, 'idSemestre' => 3, 'idEspecialidad' => 3]);
            PlanAsignatura::updateOrCreate(['id' => 73, 'idAsignatura' => 73, 'idSemestre' => 3, 'idEspecialidad' => 3]);
            /* cuarto semestre */
            PlanAsignatura::updateOrCreate(['id' => 74, 'idAsignatura' => 74, 'idSemestre' => 4, 'idEspecialidad' => 3]);
            PlanAsignatura::updateOrCreate(['id' => 75, 'idAsignatura' => 75, 'idSemestre' => 4, 'idEspecialidad' => 3]);
            /* quinto semestre */
            PlanAsignatura::updateOrCreate(['id' => 76, 'idAsignatura' => 76, 'idSemestre' => 5, 'idEspecialidad' => 3]);
            PlanAsignatura::updateOrCreate(['id' => 77, 'idAsignatura' => 77, 'idSemestre' => 5, 'idEspecialidad' => 3]);
            PlanAsignatura::updateOrCreate(['id' => 78, 'idAsignatura' => 78, 'idSemestre' => 5, 'idEspecialidad' => 3]);
            /* sexto semestre */
            PlanAsignatura::updateOrCreate(['id' => 79, 'idAsignatura' => 79, 'idSemestre' => 6, 'idEspecialidad' => 3]);
            PlanAsignatura::updateOrCreate(['id' => 80, 'idAsignatura' => 80, 'idSemestre' => 6, 'idEspecialidad' => 3]);
            PlanAsignatura::updateOrCreate(['id' => 81, 'idAsignatura' => 81, 'idSemestre' => 6, 'idEspecialidad' => 3]);
        });
    }
}
