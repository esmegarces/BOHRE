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
        Schema::table('plan_asignatura', function (Blueprint $table) {
            $table->foreign(['idAsignatura'], 'plan_asignatura_ibfk_1')->references(['id'])->on('asignatura')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['idSemestre'], 'plan_asignatura_ibfk_2')->references(['id'])->on('semestre')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['idEspecilidad'], 'plan_asignatura_ibfk_3')->references(['id'])->on('especialidad')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plan_asignatura', function (Blueprint $table) {
            $table->dropForeign('plan_asignatura_ibfk_1');
            $table->dropForeign('plan_asignatura_ibfk_2');
            $table->dropForeign('plan_asignatura_ibfk_3');
        });
    }
};
