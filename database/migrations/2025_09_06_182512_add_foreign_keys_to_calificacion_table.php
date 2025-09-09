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
        Schema::table('calificacion', function (Blueprint $table) {
            $table->foreign(['idClase'], 'calificacion_ibfk_1')->references(['id'])->on('clase')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['idAlumno'], 'calificacion_ibfk_2')->references(['id'])->on('alumno')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('calificacion', function (Blueprint $table) {
            $table->dropForeign('calificacion_ibfk_1');
            $table->dropForeign('calificacion_ibfk_2');
        });
    }
};
