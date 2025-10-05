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
        Schema::create('ciclo_escolar', function (Blueprint $table) {
            $table->id();
            $table->year('anioInicio');
            $table->year('anioFin');
            $table->boolean('activo')->default(true)->index('idx_ciclo_activo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ciclo_escolar');
    }
};
