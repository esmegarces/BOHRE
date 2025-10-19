<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use Illuminate\Http\Request;

class EspecialidadesController extends Controller
{
    public function index()
    {
        $especialidades = Especialidad::all();

        if ($especialidades->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron especialidades.',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Especialidades obtenidas exitosamente.',
            'data' => $especialidades
        ]);
    }
}
