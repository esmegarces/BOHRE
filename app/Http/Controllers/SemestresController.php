<?php

namespace App\Http\Controllers;

use App\Models\Semestre;
use Illuminate\Http\Request;

class SemestresController extends Controller
{
    public function index()
    {
        $semestres = Semestre::selectRaw("id, numero,  CONCAT(mesInicio, '-', diaInicio, ' / ', mesFin, '-', diaFin) as periodo")->get();

        if ($semestres->isEmpty()) {
            return response()->json([
                'message' => 'No hay semestres registrados',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Semestres obtenidos correctamente',
            'data' => $semestres
        ]);
    }
}
