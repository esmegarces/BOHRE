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
        Schema::create('alumno_especialidad', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idAlumno')->index('idAlumno');
            $table->unsignedBigInteger('idEspecialidad')->index('idEspecialidad');
            $table->smallInteger('semestreInicio');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alumno_especialidad');
    }
};
