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
        Schema::create('grupo_semestre', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('idGrupo')->index('idGrupo');
            $table->integer('idSemestre')->index('idSemestre');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grupo_semestre');
    }
};
