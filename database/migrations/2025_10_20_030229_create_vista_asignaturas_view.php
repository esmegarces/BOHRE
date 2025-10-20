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

        DB::statement("CREATE VIEW `vista_asignaturas` AS select `a`.`id` AS `idAsignatura`,`a`.`nombre` AS `nombre`,`a`.`tipo` AS `tipo`,concat(`s`.`numero`,' ','semestre') AS `semestre`,`s`.`periodo` AS `periodo`,(case `es`.`nombre` when 'SALUD' then 'SALUD' when 'ADMINISTRACIÓN' then 'ADMINISTRACIÓN' when 'ALIMENTOS' then 'ALIMENTOS' else '-' end) AS `especialidad` from (((`{$dbName}`.`asignatura` `a` join `{$dbName}`.`plan_asignatura` `pa` on((`a`.`id` = `pa`.`idAsignatura`))) left join `{$dbName}`.`especialidad` `es` on((`pa`.`idEspecilidad` = `es`.`id`))) join `{$dbName}`.`semestre` `s` on((`pa`.`idSemestre` = `s`.`id`)))");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS `vista_asignaturas`");
    }
};
