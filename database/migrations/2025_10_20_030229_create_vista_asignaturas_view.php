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
        {
            $dbName = DB::getDatabaseName();

            DB::statement("CREATE VIEW `vista_asignaturas` AS
    SELECT
        `a`.`id` AS `idAsignatura`,
        `a`.`nombre` AS `nombre`,
        `a`.`tipo` AS `tipo`,
        CONCAT(`s`.`numero`, ' ', 'semestre') AS `semestre`,
        concat(lpad(`s`.`diaInicio`, 2, '0'), '-', lpad(`s`.`mesInicio`, 2, '0'), ' / ', lpad(`s`.`diaFin`, 2, '0'), '-',
              lpad(`s`.`mesFin`, 2, '0'))         AS `periodo`,
        (CASE `es`.`nombre`
            WHEN 'SALUD' THEN 'SALUD'
            WHEN 'ADMINISTRACIÓN' THEN 'ADMINISTRACIÓN'
            WHEN 'ALIMENTOS' THEN 'ALIMENTOS'
            ELSE '-'
        END) AS `especialidad`
    FROM `{$dbName}`.`asignatura` `a`
    JOIN `{$dbName}`.`plan_asignatura` `pa` ON (`a`.`id` = `pa`.`idAsignatura`)
    LEFT JOIN `{$dbName}`.`especialidad` `es` ON (`pa`.`idEspecialidad` = `es`.`id`)
    JOIN `{$dbName}`.`semestre` `s` ON (`pa`.`idSemestre` = `s`.`id`)
    ");
        }
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
