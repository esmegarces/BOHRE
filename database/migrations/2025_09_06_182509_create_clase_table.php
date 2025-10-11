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
            $table->unsignedBigInteger('idAsignatura')->index('idAsignatura');
            $table->unsignedBigInteger('idDocente')->nullable()->index('idDocente');
            $table->unsignedBigInteger('idGrupoSemestre')->index('idGrupoSemestre');
            $table->unsignedBigInteger('idEspecialidad')->nullable()->index('idEspecialidad');
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
