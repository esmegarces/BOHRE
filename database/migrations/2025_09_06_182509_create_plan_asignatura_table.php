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
            $table->id();
            $table->unsignedBigInteger('idAsignatura')->index('idAsignatura');
            $table->unsignedBigInteger('idSemestre')->index('idSemestre');
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
        Schema::dropIfExists('plan_asignatura');
    }
};
