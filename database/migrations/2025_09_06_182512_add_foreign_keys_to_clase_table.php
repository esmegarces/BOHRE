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
        Schema::table('clase', function (Blueprint $table) {
            $table->foreign(['idAsignatura'], 'clase_ibfk_1')->references(['id'])->on('asignatura')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['idDocente'], 'clase_ibfk_2')->references(['id'])->on('docente')->onUpdate('NO ACTION')->onDelete('SET NULL');
            $table->foreign(['idGrupoSemestre'], 'clase_ibfk_3')->references(['id'])->on('grupo_semestre')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['idEspecialidad'], 'clase_ibfk_4')->references(['id'])->on('especialidad')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clase', function (Blueprint $table) {
            $table->dropForeign('clase_ibfk_1');
            $table->dropForeign('clase_ibfk_2');
            $table->dropForeign('clase_ibfk_3');
            $table->dropForeign('clase_ibfk_4');
        });
    }
};
