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
            Generacion::updateOrCreate(['fechaIngreso' => '2018-08-01', 'fechaEgreso' => '2021-07-31']);
            Generacion::updateOrCreate(['fechaIngreso' => '2019-08-01', 'fechaEgreso' => '2022-07-31']);
            Generacion::updateOrCreate(['fechaIngreso' => '2020-08-01', 'fechaEgreso' => '2023-07-31']);
            Generacion::updateOrCreate(['fechaIngreso' => '2021-08-01', 'fechaEgreso' => '2024-07-31']);
            Generacion::updateOrCreate(['fechaIngreso' => '2022-08-01', 'fechaEgreso' => '2025-07-31']);
            Generacion::updateOrCreate(['fechaIngreso' => '2023-08-01', 'fechaEgreso' => '2026-07-31']);
            Generacion::updateOrCreate(['fechaIngreso' => '2024-08-01', 'fechaEgreso' => '2027-07-31']);
            Generacion::updateOrCreate(['fechaIngreso' => '2025-08-01', 'fechaEgreso' => '2028-07-31']);
        });

    }
}
