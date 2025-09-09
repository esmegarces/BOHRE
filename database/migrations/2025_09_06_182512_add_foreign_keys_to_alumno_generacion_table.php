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
        Schema::table('alumno_generacion', function (Blueprint $table) {
            $table->foreign(['idAlumno'], 'alumno_generacion_ibfk_1')->references(['id'])->on('alumno')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['idGeneracion'], 'alumno_generacion_ibfk_2')->references(['id'])->on('generacion')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alumno_generacion', function (Blueprint $table) {
            $table->dropForeign('alumno_generacion_ibfk_1');
            $table->dropForeign('alumno_generacion_ibfk_2');
        });
    }
};
