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
        Schema::table('grupo_semestre', function (Blueprint $table) {
            $table->foreign(['idGrupo'], 'grupo_semestre_ibfk_1')->references(['id'])->on('grupo')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['idSemestre'], 'grupo_semestre_ibfk_2')->references(['id'])->on('semestre')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('grupo_semestre', function (Blueprint $table) {
            $table->dropForeign('grupo_semestre_ibfk_1');
            $table->dropForeign('grupo_semestre_ibfk_2');
        });
    }
};
