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
        Schema::create('persona', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('nombre', 20);
            $table->string('apellidoPaterno', 20);
            $table->string('apellidoMaterno', 20);
            $table->string('curp', 18)->unique('curp');
            $table->string('telefono', 15);
            $table->enum('sexo', ['F', 'M']);
            $table->integer('idDireccion')->index('idDireccion');
            $table->integer('idCuenta')->index('idCuenta');
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
        Schema::dropIfExists('persona');
    }
};
