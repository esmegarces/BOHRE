<?php

namespace App\Http\Controllers;

use App\Models\AsignaturasEspecialidadesView;
use App\Models\Especialidad;
use Illuminate\Http\Request;
use Mockery\Exception;

class EspecialidadesController extends Controller
{
    public function index()
    {
        $especialidades = Especialidad::orderBy('id')->get();

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

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|min:5|max:80|unique:especialidad,nombre',
        ]);

        try {
            $especialidad = Especialidad::create([
                'nombre' => strtoupper($request->nombre),
            ]);

            return response()->json([
                'message' => 'Especialidad creada exitosamente.',
                'data' => $especialidad
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al crear la especialidad.',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'nombre' => 'required|string|min:5|max:80|unique:especialidad,nombre,' . $id,
        ]);

        try {
            $especialidad = Especialidad::find($id);

            if (!$especialidad) {
                return response()->json([
                    'message' => 'Especialidad no encontrada.',
                    'data' => null
                ], 404);
            }

            $especialidad->update(['nombre' => strtoupper($request->nombre)]);

            return response()->json([
                'message' => 'Especialidad actualizada exitosamente.',
                'data' => $especialidad
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al crear la especialidad.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAsignaturasByEspecialidad(int $id)
    {
        $asignaturas = AsignaturasEspecialidadesView::where('idEspecialidad', $id)
            ->orderBy('semestre')
            ->get();

        if ($asignaturas->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron asignaturas para la especialidad especificada.',
                'data' => null
            ], 404);

        }

        return response()->json([
            'message' => 'Asignaturas obtenidas exitosamente.',
            'data' => $asignaturas
        ]);
    }
}
