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
        Schema::table('localidad', function (Blueprint $table) {
            $table->foreign(['idMunicipio'], 'localidad_ibfk_1')->references(['id'])->on('municipio')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('localidad', function (Blueprint $table) {
            $table->dropForeign('localidad_ibfk_1');
        });
    }
};
