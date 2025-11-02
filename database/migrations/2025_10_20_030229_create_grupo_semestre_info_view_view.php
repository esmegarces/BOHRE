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

        DB::statement("
        CREATE OR REPLACE VIEW `{$dbName}`.`grupo_semestre_info_view` AS
        SELECT
            `gs`.`id` AS `idGrupoSemestre`,
            `g`.`id` AS `idGrupo`,
            `s`.`id` AS `idSemestre`,
            `g`.`prefijo` AS `grupo`,
            `s`.`numero` AS `semestre`,
            CONCAT(LPAD(`s`.`diaInicio`, 2, '0'), '-', LPAD(`s`.`mesInicio`, 2, '0'), ' / ',
                   LPAD(`s`.`diaFin`, 2, '0'), '-', LPAD(`s`.`mesFin`, 2, '0')) AS `periodoSemestre`,
            COUNT(DISTINCT CASE
                WHEN `p`.`id` IS NOT NULL AND `c`.`id` IS NOT NULL
                THEN `ags`.`idAlumno`
                ELSE NULL
            END) AS `numeroAlumnos`,
            COUNT(DISTINCT `cl`.`idAsignatura`) AS `numeroAsignaturas`,
            (CASE
                WHEN `s`.`mesFin` < `s`.`mesInicio` THEN
                    CONCAT(YEAR(CURDATE()), '-', YEAR(CURDATE()) + 1)
                ELSE
                    CAST(YEAR(CURDATE()) AS CHAR)
            END) AS `cicloEscolar`
        FROM `{$dbName}`.`grupo_semestre` `gs`
        INNER JOIN `{$dbName}`.`grupo` `g` ON `gs`.`idGrupo` = `g`.`id`
        INNER JOIN `{$dbName}`.`semestre` `s` ON `gs`.`idSemestre` = `s`.`id`
        LEFT JOIN `{$dbName}`.`alumno_grupo_semestre` `ags` ON `ags`.`idGrupoSemestre` = `gs`.`id`
        LEFT JOIN `{$dbName}`.`alumno` `a` ON `ags`.`idAlumno` = `a`.`id`
        LEFT JOIN `{$dbName}`.`persona` `p` ON `a`.`idPersona` = `p`.`id` AND `p`.`deleted_at` IS NULL
        LEFT JOIN `{$dbName}`.`cuenta` `c` ON `p`.`idCuenta` = `c`.`id` AND `c`.`deleted_at` IS NULL
        LEFT JOIN `{$dbName}`.`clase` `cl` ON `cl`.`idGrupoSemestre` = `gs`.`id`
            AND `cl`.`anio` = YEAR(CURDATE())
            AND `cl`.`idEspecialidad` IS NULL
        WHERE
            CURDATE() BETWEEN
                CAST(CONCAT(YEAR(CURDATE()), '-', LPAD(`s`.`mesInicio`, 2, '0'), '-',
                            LPAD(`s`.`diaInicio`, 2, '0')) AS DATE)
            AND
                CAST(CONCAT(
                    (CASE WHEN `s`.`mesFin` < `s`.`mesInicio`
                          THEN YEAR(CURDATE()) + 1
                          ELSE YEAR(CURDATE()) END),
                    '-', LPAD(`s`.`mesFin`, 2, '0'), '-', LPAD(`s`.`diaFin`, 2, '0')
                ) AS DATE)
        GROUP BY `gs`.`id`, `g`.`id`, `s`.`id`, `g`.`prefijo`, `s`.`numero`,
                 `s`.`diaInicio`, `s`.`mesInicio`, `s`.`diaFin`, `s`.`mesFin`
        ORDER BY `s`.`numero`
    ");
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
