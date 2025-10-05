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
        Schema::create('alumno_generacion', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('semestreInicial');
            $table->unsignedBigInteger('idAlumno')->index('idAlumno');
            $table->unsignedBigInteger('idGeneracion')->index('idGeneracion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alumno_generacion');
    }
};
