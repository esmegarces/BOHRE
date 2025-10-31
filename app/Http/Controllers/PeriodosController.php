<?php

namespace App\Http\Controllers;

use App\Models\Generacion;
use App\Models\GrupoSemestre;
use App\Models\Semestre;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeriodosController extends Controller
{
    public function getGeneraciones(Request $request)
    {

        // Validar el parámetro 'current'
        $current = $request->input('current');


        // Obtener todas las generaciones
        $generaciones = [];

        if ($current) {
            $generaciones = Generacion::where('fechaIngreso', '<=', now())
                ->where('fechaEgreso', '>=', now())
                ->get();
        } else {
            $generaciones = Generacion::all();
        }

        // Verificar si se encontraron generaciones
        if ($generaciones->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron generaciones.',
                'data' => null
            ], 404);
        }


        return response()->json([
            'message' => 'Generaciones obtenidas exitosamente.',
            'data' => $generaciones
        ]);
    }

    public function getGrupoSemestre()
    {
        $gruposSemestres = GrupoSemestre::join('grupo as g', 'grupo_semestre.idGrupo', '=', 'g.id')
            ->join('semestre as s', 'grupo_semestre.idSemestre', '=', 's.id')
            ->whereRaw("
        CURDATE() BETWEEN
            CAST(CONCAT(
                YEAR(CURDATE()),
                '-',
                LPAD(s.mesInicio, 2, '0'),
                '-',
                LPAD(s.diaInicio, 2, '0')
            ) AS DATE)
        AND
            CAST(CONCAT(
                (CASE
                    WHEN s.mesFin < s.mesInicio
                    THEN YEAR(CURDATE()) + 1
                    ELSE YEAR(CURDATE())
                END),
                '-',
                LPAD(s.mesFin, 2, '0'),
                '-',
                LPAD(s.diaFin, 2, '0')
            ) AS DATE)
    ")
            ->select(
                'grupo_semestre.id',
                'g.prefijo AS nombreGrupo',
                's.numero AS numeroSemestre',
                DB::raw("CONCAT(s.diaInicio, '/', s.mesInicio, ' - ', s.diaFin, '/', s.mesFin) AS periodoSemestre")
            )
            ->orderBy('s.numero', 'asc')
            ->get();

        // Verificar si se encontraron grupos semestre
        if ($gruposSemestres->isEmpty()) {
            return response()->json([
                'message' => 'No hay grupos-semestre activos en este momento. Ejecute: POST /api/clases/generar para crear las clases del ciclo actual.',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Grupos-Semestre obtenidos exitosamente.',
            'data' => $gruposSemestres
        ]);
    }

    public function getSemestres()
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
