<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $dbName = DB::getDatabaseName();

        DB::statement("CREATE VIEW `view_alumno_asignaturas` AS select `a`.`id` AS `idAlumno`,`p`.`id` AS `idPersona`,`p`.`nombre` AS `nombre`,`p`.`apellidoPaterno` AS `apellidoPaterno`,`p`.`apellidoMaterno` AS `apellidoMaterno`,`p`.`fechaNacimiento` AS `fechaNacimiento`,`p`.`curp` AS `curp`,`p`.`nss` AS `nss`,`c`.`correo` AS `correo`,`p`.`telefono` AS `telefono`,`c`.`rol` AS `rol`,if((`p`.`sexo` = 'M'),'Masculino','Femenino') AS `sexo`,`a`.`nia` AS `nia`,`a`.`situacion` AS `situacion`,concat(`ci`.`anioFin`,'-',(`ci`.`anioFin` + 1)) AS `cicloEscolar`,concat(`g`.`anioIngreso`,'/',`g`.`anioEgreso`) AS `generacion`,`e`.`nombre` AS `especialidad`,concat(`s`.`numero`,'-',`grup`.`prefijo`) AS `grupo`,(case when (count(`cl`.`id`) > 0) then json_arrayagg(json_object('materia',`asg`.`nombre`,'salon',`cl`.`salonClase`,'momento1',`cal`.`momento1`,'momento2',`cal`.`momento2`,'momento3',`cal`.`momento3`,'idDocente',`doc`.`id`,'docente',concat(`pdoc`.`nombre`,' ',`pdoc`.`apellidoPaterno`,' ',`pdoc`.`apellidoMaterno`))) else json_array() end) AS `asignaturas` from ((((((((((((((((((`{$dbName}`.`cuenta` `c` join `{$dbName}`.`persona` `p` on((`c`.`id` = `p`.`idCuenta`))) join `{$dbName}`.`alumno` `a` on((`p`.`id` = `a`.`idPersona`))) join `{$dbName}`.`alumno_generacion` `ag` on((`a`.`id` = `ag`.`idAlumno`))) join `{$dbName}`.`generacion` `g` on((`ag`.`idGeneracion` = `g`.`id`))) join `{$dbName}`.`alumno_ciclo` `ac` on((`a`.`id` = `ac`.`idAlumno`))) join `{$dbName}`.`ciclo_escolar` `ci` on((`ac`.`idCicloEscolar` = `ci`.`id`))) left join `{$dbName}`.`alumno_especialidad` `aesp` on((`a`.`id` = `aesp`.`idAlumno`))) left join `{$dbName}`.`especialidad` `e` on((`aesp`.`idEspecialidad` = `e`.`id`))) join `{$dbName}`.`alumno_grupo_semestre` `ags` on((`a`.`id` = `ags`.`idAlumno`))) join `{$dbName}`.`grupo_semestre` `gs` on((`ags`.`idGrupoSemestre` = `gs`.`id`))) join `{$dbName}`.`semestre` `s` on((`gs`.`idSemestre` = `s`.`id`))) join `{$dbName}`.`grupo` `grup` on((`gs`.`idGrupo` = `grup`.`id`))) left join `{$dbName}`.`clase` `cl` on((`gs`.`id` = `cl`.`idGrupoSemestre`))) left join `{$dbName}`.`calificacion` `cal` on(((`cl`.`id` = `cal`.`idClase`) and (`a`.`id` = `cal`.`idAlumno`)))) left join `{$dbName}`.`asignatura` `asg` on((`cl`.`idAsignatura` = `asg`.`id`))) left join `{$dbName}`.`plan_asignatura` `pa` on(((`asg`.`id` = `pa`.`idAsignatura`) and (`s`.`id` = `pa`.`idSemestre`) and ((`pa`.`idEspecilidad` = `aesp`.`idEspecialidad`) or (`pa`.`idEspecilidad` is null))))) left join `{$dbName}`.`docente` `doc` on((`cl`.`idDocente` = `doc`.`id`))) left join `{$dbName}`.`persona` `pdoc` on((`doc`.`idPersona` = `pdoc`.`id`))) where ((`pa`.`id` is not null) or (`cl`.`id` is null)) group by `a`.`id`,`p`.`nombre`,`p`.`sexo`,`a`.`nia`,`a`.`situacion`,`ci`.`id`,`g`.`id`,`e`.`id`,`gs`.`id`");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS `view_alumno_asignaturas`");
    }
};
