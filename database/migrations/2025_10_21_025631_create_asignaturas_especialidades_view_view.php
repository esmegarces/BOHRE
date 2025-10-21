<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $dbName = DB::getDatabaseName();

        DB::statement("CREATE VIEW `asignaturas_especialidades_view` AS select `asg`.`id` AS `id`,`asg`.`nombre` AS `asignatura`,`asg`.`tipo` AS `tipo`,`esp`.`id` AS `idEspecialidad`,`esp`.`nombre` AS `especialidad`,`s`.`numero` AS `semestre` from (((`{$dbName}`.`asignatura` `asg` left join `{$dbName}`.`plan_asignatura` `planasig` on((`asg`.`id` = `planasig`.`idAsignatura`))) left join `{$dbName}`.`especialidad` `esp` on((`planasig`.`idEspecilidad` = `esp`.`id`))) join `{$dbName}`.`semestre` `s` on((`planasig`.`idSemestre` = `s`.`id`)))");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS `asignaturas_especialidades_view`");
    }
};
