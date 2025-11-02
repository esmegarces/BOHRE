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
        Schema::table('alumno_especialidad', function (Blueprint $table) {
            $table->foreign(['idAlumno'], 'alumno_especialidad_ibfk_1')->references(['id'])->on('alumno')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['idEspecialidad'], 'alumno_especialidad_ibfk_2')->references(['id'])->on('especialidad')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alumno_especialidad', function (Blueprint $table) {
            $table->dropForeign('alumno_especialidad_ibfk_1');
            $table->dropForeign('alumno_especialidad_ibfk_2');
        });
    }
};
