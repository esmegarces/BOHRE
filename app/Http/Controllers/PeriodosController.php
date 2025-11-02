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

    public function createGeneracion(Request $request)
    {
        $request->validate([
            'fechaIngreso' => 'required|date',
            'fechaEgreso' => 'required|date|after:fechaIngreso',
        ]);

        try {


            $generacion = Generacion::create([
                'fechaIngreso' => $request->input('fechaIngreso'),
                'fechaEgreso' => $request->input('fechaEgreso'),
            ]);

            return response()->json([
                'message' => 'Generación creada exitosamente.',
                'data' => $generacion
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear la generación: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function getGeneracionesWithAlumnos()
    {
        $generaciones = Generacion::leftJoin('alumno_generacion as ag', 'generacion.id', '=', 'ag.idGeneracion')
            ->select(
                'generacion.id',
                'generacion.fechaIngreso',
                'generacion.fechaEgreso',
                DB::raw('COUNT(ag.idAlumno) as numeroAlumnos')
            )
            ->groupBy('generacion.id', 'generacion.fechaIngreso', 'generacion.fechaEgreso')
            ->orderBy('generacion.fechaIngreso', 'desc')
            ->get();

        // Verificar si se encontraron generaciones
        if ($generaciones->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron generaciones.',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Generaciones con alumnos obtenidas exitosamente.',
            'data' => $generaciones
        ]);
    }

    public function getAlumnosGeneraciones($id)
    {
        $alumnosGeneraciones = Generacion::leftJoin('alumno_generacion as ag', 'generacion.id', '=', 'ag.idGeneracion')
            ->join('alumno as a', 'ag.idAlumno', '=', 'a.id')
            ->join('persona as p', 'a.idPersona', '=', 'p.id')
            ->select(
                'p.id as id',
                'a.nia',
                'p.nombre',
                'p.apellidoPaterno',
                'p.apellidoMaterno',
                'generacion.id as idGeneracion',
                'generacion.fechaIngreso',
                'generacion.fechaEgreso',
            )
            ->where('generacion.id', $id)
            ->orderBy('generacion.fechaIngreso', 'desc')
            ->get();

        // Verificar si se encontraron alumnos en generaciones
        if ($alumnosGeneraciones->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron alumnos en generaciones.',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Alumnos en generaciones obtenidos exitosamente.',
            'data' => $alumnosGeneraciones
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

    public function getSemestresRAW()
    {
        $semestres = Semestre::all();

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

    public function updateSemestres(Request $request)
    {
        $request->validate([
            'semestres' => 'required|numeric|in:1,2', // 1 para impares y 2 para pares
            'mesInicio' => 'required|numeric|min:1|max:12',
            'diaInicio' => 'required|numeric|min:1|max:31',
            'mesFin' => 'required|numeric|min:1|max:12',
            'diaFin' => 'required|numeric|min:1|max:31',
        ]);

        try {
            $updatedSemestres = $request->semestres == 1 ? [1, 3, 5] : [2, 4, 6];


            $semestres = Semestre::whereIn('numero', $updatedSemestres)->get();

            $newsemestres = DB::transaction(function () use ($semestres, $request) {
                foreach ($semestres as $semestre) {
                    $semestre->mesInicio = $request->mesInicio;
                    $semestre->diaInicio = $request->diaInicio;
                    $semestre->mesFin = $request->mesFin;
                    $semestre->diaFin = $request->diaFin;
                    $semestre->save();
                }
            });


            return response()->json([
                'message' => 'Semestres actualizados exitosamente.',
                'data' => $newsemestres
            ]);

        } catch (\Exception $th) {
            return response()->json([
                'message' => 'Error al actualizar los semestres: ' . $th->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
