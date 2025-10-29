<?php

namespace App\Http\Controllers;

use App\Services\ClaseGeneratorService;
use Illuminate\Http\Request;

class ClaseController extends Controller
{
    protected $service;

    public function __construct(ClaseGeneratorService $service)
    {
        $this->service = $service;
    }

    /**
     * Endpoint para generar clases desde el frontend.
     * GET o POST /api/clases/generar
     */
    public function generar(Request $request)
    {
        try {
            $semestres = $request->input('semestres'); // opcional

            $resultado = $this->service->generarClases($semestres);

            return response()->json([
                'message' => 'Clases generadas correctamente',
                'data' => $resultado
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al generar las clases: ' . $e->getMessage()
            ], 500);
        }

    }

    /**
     * Endpoint para consultar los semestres activos.
     * GET /api/semestres/activos
     */
    public function semestresActivos()
    {
        return response()->json([
            'semestres_activos' => $this->service->detectarSemestresActivos(),
        ]);
    }
}
