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
            Municipio::updateOrCreate(['id' => 1, 'nombre' => 'TETELES DE AVILA CASTILLO']);
            Municipio::updateOrCreate(['id' => 2, 'nombre' => 'TLATLAUQUITEPEC']);
            Municipio::updateOrCreate(['id' => 3, 'nombre' => 'YAONAHUAC']);
            Municipio::updateOrCreate(['id' => 4, 'nombre' => 'CHIGNAUTLA']);
            Municipio::updateOrCreate(['id' => 5, 'nombre' => 'HUEYAPAN']);
            Municipio::updateOrCreate(['id' => 6, 'nombre' => 'TEZIUTLAN']);
            Municipio::updateOrCreate(['id' => 7, 'nombre' => 'ATEMPAN']);
        });


    }
}
