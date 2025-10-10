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
            Semestre::updateOrCreate(['id' => 1, 'numero' => 1, 'periodo' => 'AGO/DIC']);
            Semestre::updateOrCreate(['id' => 2, 'numero' => 2, 'periodo' => 'FEB/AGO']);
            Semestre::updateOrCreate(['id' => 3, 'numero' => 3, 'periodo' => 'AGO/DIC']);
            Semestre::updateOrCreate(['id' => 4, 'numero' => 4, 'periodo' => 'FEB/AGO']);
            Semestre::updateOrCreate(['id' => 5, 'numero' => 5, 'periodo' => 'AGO/DIC']);
            Semestre::updateOrCreate(['id' => 6, 'numero' => 6, 'periodo' => 'FEB/AGO']);
        });
    }
}
