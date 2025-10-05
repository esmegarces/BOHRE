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
        Schema::create('clase', function (Blueprint $table) {
            $table->id();
            $table->string('salonClase', 8);
            $table->integer('idAsignatura')->index('idAsignatura');
            $table->integer('idDocente')->index('idDocente');
            $table->integer('idGrupoSemestre')->index('idGrupoSemestre');
            $table->integer('idEspecialidad')->nullable()->index('idEspecialidad');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clase');
    }
};
