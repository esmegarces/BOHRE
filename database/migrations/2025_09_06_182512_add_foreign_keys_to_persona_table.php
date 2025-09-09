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
        Schema::table('persona', function (Blueprint $table) {
            $table->foreign(['idDireccion'], 'persona_ibfk_1')->references(['id'])->on('direccion')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['idCuenta'], 'persona_ibfk_2')->references(['id'])->on('cuenta')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('persona', function (Blueprint $table) {
            $table->dropForeign('persona_ibfk_1');
            $table->dropForeign('persona_ibfk_2');
        });
    }
};
