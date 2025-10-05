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
        Schema::create('alumno_ciclo', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('semestreCursado');
            $table->integer('idAlumno')->index('idAlumno');
            $table->integer('idCicloEscolar')->index('idCicloEscolar');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alumno_ciclo');
    }
};
