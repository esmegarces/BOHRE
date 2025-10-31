<?php

namespace App\Http\Controllers;

use App\Models\AlumnosGruposView;
use App\Models\Clase;
use App\Models\Especialidad;
use App\Models\GrupoSemestreInfoView;
use App\Models\VistaClasesGrupoSemestre;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GrupoSemestreInfoViewController extends Controller
{

    /**
     * Recupera informacion general de los grupos semestres activos
     * @return JsonResponse
     */
    public function index()
    {
        $gruposSemestreInfo = GrupoSemestreInfoView::paginate(15);

        if ($gruposSemestreInfo->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron registros de grupos semestre info.'
            ], 404);
        }

        return response()->json(['message' => 'informacion recuperada con exito', 'data' => $gruposSemestreInfo]);
    }

    /**
     * Obtener informacion extra detallada de un grupo-semestre, alumnos, clases, etc.
     * @param int $id
     * @return JsonResponse
     */
    function showExtraInfo(int $id): JsonResponse
    {
        // Obtener info general del grupo-semestre
        $grupoSemestreInfo = AlumnosGruposView::where('idGrupoSemestre', $id)->first();

        if (!$grupoSemestreInfo) {
            return response()->json([
                'message' => 'No se encontró información para el idGrupoSemestre proporcionado.'
            ], 404);
        }

        // Obtener año actual
        $anioActual = now()->year;

        // Obtener las CLASES reales que existen para este grupo-semestre
        $clases = VistaClasesGrupoSemestre::where('idGrupoSemestre', $id)
            ->where('anio', $anioActual)
            ->orderBy('tipoAsignatura', 'desc') // COMUN primero
            ->orderBy('especialidad', 'asc')
            ->orderBy('nombreAsignatura', 'asc')
            ->get();

        // Validar si existen clases
        if ($clases->isEmpty()) {
            return response()->json([
                'message' => "No existen clases creadas para este grupo-semestre en el año {$anioActual}",
                'data' => [
                    "general" => $grupoSemestreInfo,
                    "clases" => [
                        "troncoComun" => [],
                        "especialidades" => []
                    ],
                    "anio" => $anioActual,
                    "estadisticas" => [
                        "totalClases" => 0,
                        "clasesConDocente" => 0,
                        "clasesSinDocente" => 0
                    ],
                    "advertencia" => "No hay clases creadas {$anioActual}."
                ]
            ], 200);
        }

        // Agrupar clases
        $troncoComun = $clases->filter(function ($clase) {
            return $clase->tipoAsignatura === 'COMUN' && $clase->especialidad === 'TRONCO COMÚN';
        })->values();

        $especialidades = $clases->filter(function ($clase) {
            return $clase->tipoAsignatura === 'ESPECIALIDAD';
        })->groupBy('especialidad')
            ->map(fn($grupo) => $grupo->values());

        // Calcular estadísticas
        $estadisticas = [
            "totalClases" => $clases->count(),
            "clasesConDocente" => $clases->whereNotNull('idDocente')->count(),
            "clasesSinDocente" => $clases->whereNull('idDocente')->count(),
        ];

        return response()->json([
            'message' => 'Información recuperada con éxito',
            'data' => [
                "general" => $grupoSemestreInfo,
                "clases" => [
                    "troncoComun" => $troncoComun,
                    "especialidades" => $especialidades
                ],
                "anio" => $anioActual,
                "estadisticas" => $estadisticas
            ]
        ], 200);
    }




}
