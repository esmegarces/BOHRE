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
        Schema::create('docente', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('cedulaProfesional', 13);
            $table->integer('numeroExpediente');
            $table->integer('idPersona')->index('idPersona');

            $table->unique(['cedulaProfesional', 'numeroExpediente'], 'cedulaProfesional');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('docente');
    }
};
