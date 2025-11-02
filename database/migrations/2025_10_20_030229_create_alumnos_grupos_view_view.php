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

        DB::statement("CREATE VIEW `alumnos_grupos_view` AS
SELECT
    `gs`.`id` AS `idGrupoSemestre`,
    `g`.`prefijo` AS `grupo`,
    `s`.`numero` AS `semestre`,
    CONCAT(
        LPAD(`s`.`diaInicio`, 2, '0'), '-', LPAD(`s`.`mesInicio`, 2, '0'),
        ' / ',
        LPAD(`s`.`diaFin`, 2, '0'), '-', LPAD(`s`.`mesFin`, 2, '0')
    ) AS `periodoSemestre`,
    JSON_ARRAYAGG(
        JSON_OBJECT(
            'id', `a`.`id`,
            'personaId', `p`.`id`,
            'nia', `a`.`nia`,
            'nombre', `p`.`nombre`,
            'apellidoPaterno', `p`.`apellidoPaterno`,
            'apellidoMaterno', `p`.`apellidoMaterno`,
            'especialidad', `esp`.`nombre`
        )
    ) AS `alumnos`
FROM `{$dbName}`.`grupo_semestre` `gs`
JOIN `{$dbName}`.`grupo` `g` ON (`gs`.`idGrupo` = `g`.`id`)
JOIN `{$dbName}`.`semestre` `s` ON (`gs`.`idSemestre` = `s`.`id`)
LEFT JOIN `{$dbName}`.`alumno_grupo_semestre` `ags` ON (`ags`.`idGrupoSemestre` = `gs`.`id`)
LEFT JOIN `{$dbName}`.`alumno` `a` ON (`ags`.`idAlumno` = `a`.`id`)
LEFT JOIN `{$dbName}`.`persona` `p` ON (`a`.`idPersona` = `p`.`id`) AND `p`.`deleted_at` IS NULL
LEFT JOIN `{$dbName}`.`cuenta` `c` ON (`p`.`idCuenta` = `c`.`id`) AND `c`.`deleted_at` IS NULL
LEFT JOIN `{$dbName}`.`alumno_especialidad` `aesp` ON (`aesp`.`idAlumno` = `a`.`id`)
LEFT JOIN `{$dbName}`.`especialidad` `esp` ON (`aesp`.`idEspecialidad` = `esp`.`id`)
WHERE `p`.`id` IS NOT NULL AND `c`.`id` IS NOT NULL
GROUP BY
    `gs`.`id`,
    `g`.`prefijo`,
    `s`.`numero`,
    `s`.`diaInicio`,
    `s`.`mesInicio`,
    `s`.`diaFin`,
    `s`.`mesFin`
");
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
