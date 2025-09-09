<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alumno_grupo_semestre', function (Blueprint $table) {
            $table->foreign(['idAlumno'], 'alumno_grupo_semestre_ibfk_1')->references(['id'])->on('alumno')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['idGrupoSemestre'], 'alumno_grupo_semestre_ibfk_2')->references(['id'])->on('grupo_semestre')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alumno_grupo_semestre', function (Blueprint $table) {
            $table->dropForeign('alumno_grupo_semestre_ibfk_1');
            $table->dropForeign('alumno_grupo_semestre_ibfk_2');
        });
    }
};
