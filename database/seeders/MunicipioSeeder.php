<?php

namespace Database\Seeders;

use App\Models\Municipio;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MunicipioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        \DB::transaction(function () {
            Municipio::create(['id' => 1, 'nombre' => 'TETELES DE AVILA CASTILLO']);
            Municipio::create(['id' => 2, 'nombre' => 'TLATLAUQUITEPEC']);
            Municipio::create(['id' => 3, 'nombre' => 'YAONAHUAC']);
            Municipio::create(['id' => 4, 'nombre' => 'CHIGNAUTLA']);
            Municipio::create(['id' => 5, 'nombre' => 'HUEYAPAN']);
            Municipio::create(['id' => 6, 'nombre' => 'TEZIUTLAN']);
        });


    }
}
