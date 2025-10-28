<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    // Migration: crear vista de clases reales
    public function up()
    {
        $dbName = DB::getDatabaseName();

        DB::statement("
        CREATE OR REPLACE VIEW `{$dbName}`.`vista_clases_grupo_semestre` AS
        SELECT
            `cl`.`id` AS `idClase`,
            `cl`.`anio` AS `anio`,
            `gs`.`id` AS `idGrupoSemestre`,
            `s`.`numero` AS `semestre`,
            `g`.`prefijo` AS `grupo`,
            `a`.`id` AS `idAsignatura`,
            `a`.`nombre` AS `nombreAsignatura`,
            `a`.`tipo` AS `tipoAsignatura`,
            `esp`.`id` AS `idEspecialidad`,
            COALESCE(`esp`.`nombre`, 'TRONCO COMÚN') AS `especialidad`,
            `d`.`id` AS `idDocente`,
            CONCAT_WS(' ', `p`.`nombre`, `p`.`apellidoPaterno`, `p`.`apellidoMaterno`) AS `nombreDocente`,
            -- Contar alumnos inscritos en esta clase
            COUNT(DISTINCT `cal`.`idAlumno`) AS `alumnosInscritos`
        FROM `{$dbName}`.`clase` `cl`
        INNER JOIN `{$dbName}`.`grupo_semestre` `gs` ON `cl`.`idGrupoSemestre` = `gs`.`id`
        INNER JOIN `{$dbName}`.`semestre` `s` ON `gs`.`idSemestre` = `s`.`id`
        INNER JOIN `{$dbName}`.`grupo` `g` ON `gs`.`idGrupo` = `g`.`id`
        INNER JOIN `{$dbName}`.`asignatura` `a` ON `cl`.`idAsignatura` = `a`.`id`
        LEFT JOIN `{$dbName}`.`especialidad` `esp` ON `cl`.`idEspecialidad` = `esp`.`id`
        LEFT JOIN `{$dbName}`.`docente` `d` ON `cl`.`idDocente` = `d`.`id`
        LEFT JOIN `{$dbName}`.`persona` `p` ON `d`.`idPersona` = `p`.`id`
        LEFT JOIN `{$dbName}`.`calificacion` `cal` ON `cal`.`idClase` = `cl`.`id`
        GROUP BY
            `cl`.`id`, `cl`.`anio`, `gs`.`id`, `s`.`numero`, `g`.`prefijo`,
            `a`.`id`, `a`.`nombre`, `a`.`tipo`,
            `esp`.`id`, `esp`.`nombre`,
            `d`.`id`, `p`.`nombre`, `p`.`apellidoPaterno`, `p`.`apellidoMaterno`
    ");
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS `vista_clases_grupo_semestre`");

    }
};
