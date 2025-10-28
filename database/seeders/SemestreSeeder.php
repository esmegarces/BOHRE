<?php

namespace Database\Seeders;

use App\Models\Semestre;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SemestreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::transaction(function () {
            Semestre::updateOrCreate(['id' => 1, 'numero' => 1, 'mesInicio' => 9, 'diaInicio' => 1, 'mesFin' => 1, 'diaFin' => 30]);
            Semestre::updateOrCreate(['id' => 5, 'numero' => 5, 'mesInicio' => 9, 'diaInicio' => 1, 'mesFin' => 1, 'diaFin' => 30]);
            Semestre::updateOrCreate(['id' => 3, 'numero' => 3, 'mesInicio' => 9, 'diaInicio' => 1, 'mesFin' => 1, 'diaFin' => 30]);
            Semestre::updateOrCreate(['id' => 2, 'numero' => 2, 'mesInicio' => 2, 'diaInicio' => 13, 'mesFin' => 7, 'diaFin' => 25]);
            Semestre::updateOrCreate(['id' => 4, 'numero' => 4, 'mesInicio' => 2, 'diaInicio' => 13, 'mesFin' => 7, 'diaFin' => 25]);
            Semestre::updateOrCreate(['id' => 6, 'numero' => 6, 'mesInicio' => 2, 'diaInicio' => 13, 'mesFin' => 7, 'diaFin' => 25]);
        });
    }
}
