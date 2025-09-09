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
        Schema::table('alumno', function (Blueprint $table) {
            $table->foreign(['idPersona'], 'alumno_ibfk_1')->references(['id'])->on('persona')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alumno', function (Blueprint $table) {
            $table->dropForeign('alumno_ibfk_1');
        });
    }
};
