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
        Schema::create('plan_asignatura', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('idAsignatura')->index('idAsignatura');
            $table->integer('idSemestre')->index('idSemestre');
            $table->integer('idEspecilidad')->nullable()->index('idEspecilidad');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plan_asignatura');
    }
};
