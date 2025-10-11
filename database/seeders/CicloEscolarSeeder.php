<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CicloEscolar;

class CicloEscolarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::transaction(function () {
            CicloEscolar::updateOrCreate(['anioInicio' => '2018', 'anioFin' => '2019']);
            CicloEscolar::updateOrCreate(['anioInicio' => '2019', 'anioFin' => '2020']);
            CicloEscolar::updateOrCreate(['anioInicio' => '2020', 'anioFin' => '2021']);
            CicloEscolar::updateOrCreate(['anioInicio' => '2021', 'anioFin' => '2022']);
            CicloEscolar::updateOrCreate(['anioInicio' => '2022', 'anioFin' => '2023']);
            CicloEscolar::updateOrCreate(['anioInicio' => '2023', 'anioFin' => '2024']);
            CicloEscolar::updateOrCreate(['anioInicio' => '2024', 'anioFin' => '2025']);
            CicloEscolar::updateOrCreate(['anioInicio' => '2025', 'anioFin' => '2026']);
        });
    }
}
