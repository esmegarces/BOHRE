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
        Schema::create('alumno_grupo_semestre', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('idAlumno')->index('idAlumno');
            $table->integer('idGrupoSemestre')->index('idGrupoSemestre');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alumno_grupo_semestre');
    }
};
