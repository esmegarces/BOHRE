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
        Schema::table('docente', function (Blueprint $table) {
            $table->foreign(['idPersona'], 'docente_ibfk_1')->references(['id'])->on('persona')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('docente', function (Blueprint $table) {
            $table->dropForeign('docente_ibfk_1');
        });
    }
};
