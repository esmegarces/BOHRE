<?php

namespace App\Http\Controllers;

use App\Models\Generacion;
use App\Models\GrupoSemestre;
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
        // Obtener todos los grupos semestre con la información requerida
        $gruposSemestres = GrupoSemestre::join('grupo as g', 'grupo_semestre.idGrupo', '=', 'g.id')
            ->join('semestre as s', 'grupo_semestre.idSemestre', '=', 's.id')
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
                'message' => 'No se encontraron grupos semestre.',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Grupos semestre obtenidos exitosamente.',
            'data' => $gruposSemestres
        ]);
    }
}
