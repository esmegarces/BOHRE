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

        DB::statement("CREATE VIEW `grupo_semestre_info_view` AS select `gs`.`id` AS `idGrupoSemestre`,`g`.`id` AS `idGrupo`,`s`.`id` AS `idSemestre`,`g`.`prefijo` AS `grupo`,`s`.`numero` AS `semestre`,`s`.`periodo` AS `periodoSemestre`,count(distinct `ags`.`idAlumno`) AS `numeroAlumnos`,count(distinct `pa`.`idAsignatura`) AS `numeroAsignaturas` from ((((`{$dbName}`.`grupo_semestre` `gs` join `{$dbName}`.`grupo` `g` on((`gs`.`idGrupo` = `g`.`id`))) join `{$dbName}`.`semestre` `s` on((`gs`.`idSemestre` = `s`.`id`))) left join `{$dbName}`.`alumno_grupo_semestre` `ags` on((`ags`.`idGrupoSemestre` = `gs`.`id`))) join `{$dbName}`.`plan_asignatura` `pa` on((`s`.`id` = `pa`.`idSemestre`))) where ((`pa`.`idEspecilidad` is null) and (lower(`s`.`periodo`) = (case when (curdate() <= concat(year(curdate()),'-08-01')) then 'feb/ago' else 'ago/dic' end))) group by `gs`.`id`,`g`.`id`,`s`.`id`,`g`.`prefijo`,`s`.`numero`,`s`.`periodo` order by `s`.`numero`");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS `grupo_semestre_info_view`");
    }
};
