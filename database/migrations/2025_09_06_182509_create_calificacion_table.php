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
        Schema::create('calificacion', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->smallInteger('momento1');
            $table->smallInteger('momento2');
            $table->smallInteger('momento3');
            $table->integer('idClase')->index('idClase');
            $table->integer('idAlumno')->index('idAlumno');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calificacion');
    }
};
