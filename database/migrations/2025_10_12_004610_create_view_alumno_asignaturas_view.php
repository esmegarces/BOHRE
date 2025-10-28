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
//        {
//            {
//                $dbName = DB::getDatabaseName();
//
//                DB::statement("CREATE VIEW `view_alumno_asignaturas` AS
//    SELECT
//        `a`.`id` AS `idAlumno`,
//        `p`.`id` AS `idPersona`,
//        `p`.`nombre` AS `nombre`,
//        `p`.`apellidoPaterno` AS `apellidoPaterno`,
//        `p`.`apellidoMaterno` AS `apellidoMaterno`,
//        `p`.`fechaNacimiento` AS `fechaNacimiento`,
//        `p`.`curp` AS `curp`,
//        `p`.`nss` AS `nss`,
//        `c`.`correo` AS `correo`,
//        `p`.`telefono` AS `telefono`,
//        `c`.`rol` AS `rol`,
//        IF((`p`.`sexo` = 'M'), 'Masculino', 'Femenino') AS `sexo`,
//        `a`.`nia` AS `nia`,
//        `a`.`situacion` AS `situacion`,
//        CONCAT(`g`.`fechaIngreso`, '/', `g`.`fechaEgreso`) AS `generacion`,
//        `e`.`nombre` AS `especialidad`,
//        CONCAT(`s`.`numero`, '-', `grup`.`prefijo`) AS `grupo`,
//        (CASE
//            WHEN SUM(CASE WHEN `cl`.`id` IS NOT NULL THEN 1 ELSE 0 END) > 0
//            THEN JSON_ARRAYAGG(
//                JSON_OBJECT(
//                    'materia', `asg`.`nombre`,
//                    'momento1', `cal`.`momento1`,
//                    'momento2', `cal`.`momento2`,
//                    'momento3', `cal`.`momento3`,
//                    'idDocente', `doc`.`id`,
//                    'docente', CONCAT(`pdoc`.`nombre`, ' ', `pdoc`.`apellidoPaterno`, ' ', `pdoc`.`apellidoMaterno`)
//                )
//            )
//            ELSE JSON_ARRAY()
//        END) AS `asignaturas`
//    FROM `{$dbName}`.`cuenta` `c`
//    JOIN `{$dbName}`.`persona` `p` ON (`c`.`id` = `p`.`idCuenta`)
//    JOIN `{$dbName}`.`alumno` `a` ON (`p`.`id` = `a`.`idPersona`)
//    JOIN `{$dbName}`.`alumno_generacion` `ag` ON (`a`.`id` = `ag`.`idAlumno`)
//    JOIN `{$dbName}`.`generacion` `g` ON (`ag`.`idGeneracion` = `g`.`id`)
//    LEFT JOIN `{$dbName}`.`alumno_especialidad` `aesp` ON (`a`.`id` = `aesp`.`idAlumno`)
//    LEFT JOIN `{$dbName}`.`especialidad` `e` ON (`aesp`.`idEspecialidad` = `e`.`id`)
//    JOIN `{$dbName}`.`alumno_grupo_semestre` `ags` ON (`a`.`id` = `ags`.`idAlumno`)
//    JOIN `{$dbName}`.`grupo_semestre` `gs` ON (`ags`.`idGrupoSemestre` = `gs`.`id`)
//    JOIN `{$dbName}`.`semestre` `s` ON (`gs`.`idSemestre` = `s`.`id`)
//    JOIN `{$dbName}`.`grupo` `grup` ON (`gs`.`idGrupo` = `grup`.`id`)
//    LEFT JOIN `{$dbName}`.`clase` `cl` ON (`gs`.`id` = `cl`.`idGrupoSemestre`)
//    LEFT JOIN `{$dbName}`.`asignatura` `asg` ON (`cl`.`idAsignatura` = `asg`.`id`)
//    LEFT JOIN `{$dbName}`.`calificacion` `cal` ON ((`asg`.`id` = `cal`.`idAsignatura`) AND (`a`.`id` = `cal`.`idAlumno`))
//    LEFT JOIN `{$dbName}`.`plan_asignatura` `pa` ON (
//        (`asg`.`id` = `pa`.`idAsignatura`)
//        AND (`s`.`id` = `pa`.`idSemestre`)
//        AND ((`pa`.`idEspecialidad` = `aesp`.`idEspecialidad`) OR (`pa`.`idEspecialidad` IS NULL))
//    )
//    LEFT JOIN `{$dbName}`.`docente` `doc` ON (`cl`.`idDocente` = `doc`.`id`)
//    LEFT JOIN `{$dbName}`.`persona` `pdoc` ON (`doc`.`idPersona` = `pdoc`.`id`)
//    WHERE ((`pa`.`id` IS NOT NULL) OR (`cl`.`id` IS NULL))
//    GROUP BY
//        `a`.`id`,
//        `p`.`id`,
//        `p`.`nombre`,
//        `p`.`apellidoPaterno`,
//        `p`.`apellidoMaterno`,
//        `p`.`fechaNacimiento`,
//        `p`.`curp`,
//        `p`.`nss`,
//        `p`.`sexo`,
//        `p`.`telefono`,
//        `c`.`correo`,
//        `c`.`rol`,
//        `a`.`nia`,
//        `a`.`situacion`,
//        `g`.`id`,
//        `g`.`fechaIngreso`,
//        `g`.`fechaEgreso`,
//        `e`.`id`,
//        `e`.`nombre`,
//        `gs`.`id`,
//        `s`.`numero`,
//        `grup`.`prefijo`
//    ");
//            }
//        }
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
