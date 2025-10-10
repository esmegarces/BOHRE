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
            Semestre::updateOrCreate(['numero' => 1, 'periodo' => 'AGO/DIC']);
            Semestre::updateOrCreate(['numero' => 2, 'periodo' => 'FEB/AGO']);
            Semestre::updateOrCreate(['numero' => 3, 'periodo' => 'AGO/DIC']);
            Semestre::updateOrCreate(['numero' => 4, 'periodo' => 'FEB/AGO']);
            Semestre::updateOrCreate(['numero' => 5, 'periodo' => 'AGO/DIC']);
            Semestre::updateOrCreate(['numero' => 6, 'periodo' => 'FEB/AGO']);
        });
    }
}
