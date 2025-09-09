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
        Schema::table('direccion', function (Blueprint $table) {
            $table->foreign(['idLocalidad'], 'direccion_ibfk_1')->references(['id'])->on('localidad')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('direccion', function (Blueprint $table) {
            $table->dropForeign('direccion_ibfk_1');
        });
    }
};
