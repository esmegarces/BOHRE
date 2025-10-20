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

        DB::statement("CREATE VIEW `alumnos_grupos_view` AS select `gs`.`id` AS `idGrupoSemestre`,`g`.`prefijo` AS `grupo`,`s`.`numero` AS `semestre`,`s`.`periodo` AS `periodoSemestre`,json_arrayagg(json_object('id',`p`.`id`,'nia',`a`.`nia`,'nombre',`p`.`nombre`,'apellidoPaterno',`p`.`apellidoPaterno`,'apellidoMaterno',`p`.`apellidoMaterno`,'especialidad',`esp`.`nombre`)) AS `alumnos` from (((((((`{$dbName}`.`grupo_semestre` `gs` join `{$dbName}`.`grupo` `g` on((`gs`.`idGrupo` = `g`.`id`))) join `{$dbName}`.`semestre` `s` on((`gs`.`idSemestre` = `s`.`id`))) left join `{$dbName}`.`alumno_grupo_semestre` `ags` on((`ags`.`idGrupoSemestre` = `gs`.`id`))) left join `{$dbName}`.`alumno` `a` on((`ags`.`idAlumno` = `a`.`id`))) left join `{$dbName}`.`persona` `p` on((`a`.`idPersona` = `p`.`id`))) left join `{$dbName}`.`alumno_especialidad` `aesp` on((`aesp`.`idAlumno` = `a`.`id`))) left join `{$dbName}`.`especialidad` `esp` on((`aesp`.`idEspecialidad` = `esp`.`id`))) group by `gs`.`id`,`g`.`prefijo`,`s`.`numero`,`s`.`periodo`");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS `alumnos_grupos_view`");
    }
};
