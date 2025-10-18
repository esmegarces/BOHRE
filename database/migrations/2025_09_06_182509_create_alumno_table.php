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
        Schema::create('alumno', function (Blueprint $table) {
            $table->id();
            $table->string('nia', 8)->unique('nia');
            //$table->integer('numeroLista')->unique('numeroLista');
            $table->enum('situacion', ['ACTIVO', 'BAJA_TEMPORAL', 'BAJA_DEFINITIVA', 'EGRESADO']);
            $table->unsignedBigInteger('idPersona')->index('idPersona');
            //$table->softDeletes();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alumno');
    }
};
