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
        Schema::create('generacion', function (Blueprint $table) {
            $table->id();
            $table->date('anioIngreso');
            $table->date('anioEgreso');
            //$table->boolean('activa')->default(true)->index('idx_generacion_activa');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('generacion');
    }
};
