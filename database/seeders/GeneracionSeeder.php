<?php

namespace Database\Seeders;

use App\Models\Generacion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GeneracionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::transaction(function () {
            Generacion::updateOrCreate(['anioIngreso' => '2018-08-01', 'anioEgreso' => '2021-07-31']);
            Generacion::updateOrCreate(['anioIngreso' => '2019-08-01', 'anioEgreso' => '2022-07-31']);
            Generacion::updateOrCreate(['anioIngreso' => '2020-08-01', 'anioEgreso' => '2023-07-31']);
            Generacion::updateOrCreate(['anioIngreso' => '2021-08-01', 'anioEgreso' => '2024-07-31']);
            Generacion::updateOrCreate(['anioIngreso' => '2022-08-01', 'anioEgreso' => '2025-07-31']);
            Generacion::updateOrCreate(['anioIngreso' => '2023-08-01', 'anioEgreso' => '2026-07-31']);
        });

    }
}
