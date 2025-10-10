<?php

namespace Database\Seeders;

use App\Models\Grupo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GrupoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::transaction(function () {
            Grupo::updateOrCreate(['prefijo' => 'A']);
            Grupo::updateOrCreate(['prefijo' => 'B']);
            Grupo::updateOrCreate(['prefijo' => 'C']);
        });
    }
}
