<?php
// app/Http/Controllers/ClaseController.php

namespace App\Http\Controllers;

use App\Exports\CalificacionesEspecialidadExport;
use App\Exports\CalificacionesExport;
use App\Models\Clase;
use App\Models\Especialidad;
use App\Models\GrupoSemestreInfoView;
use App\Services\ClaseGeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;


class ClaseController extends Controller
{
    protected $service;

    public function __construct(ClaseGeneratorService $service)
    {
        $this->service = $service;
    }

    /**
     * Endpoint para generar clases Y migrar alumnos automáticamente.
     * POST /api/clases/generar
     */
    public function generar(Request $request)
    {
        try {
            $semestres = $request->input('semestres'); // opcional

            $resultado = $this->service->generarClases($semestres);

            if (!$resultado['success']) {
                return response()->json([
                    'message' => 'Error al generar clases',
                    'data' => $resultado
                ], 500);
            }

            return response()->json([
                'message' => 'Clases generadas y alumnos migrados correctamente',
                'data' => $resultado
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al generar las clases: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Asignar o desasignar un docente a una clase.
     * @param Request $request
     * @param int $idClase
     * @return JsonResponse
     */
    public function asignarDocente(Request $request, int $idClase): JsonResponse
    {
        $validator = \Validator::make($request->all(), [
            'idDocente' => 'nullable|exists:docente,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $clase = Clase::find($idClase);

        if (!$clase) {
            return response()->json([
                'message' => 'Clase no encontrada'
            ], 404);
        }

        // Permitir asignar o desasignar (null)
        $clase->idDocente = $request->idDocente;
        $clase->save();

        // Obtener información completa actualizada
        $claseActualizada = Clase::with([
            'asignatura:id,nombre',
            'docente.persona:id,nombre,apellidoPaterno,apellidoMaterno'
        ])->find($idClase);

        return response()->json([
            'message' => $request->idDocente
                ? 'Docente asignado con éxito'
                : 'Docente desasignado con éxito',
            'data' => [
                'idClase' => $claseActualizada->id,
                'idDocente' => $claseActualizada->idDocente,
                'nombreDocente' => $claseActualizada->docente
                    ? $claseActualizada->docente->persona->nombre . ' ' .
                    $claseActualizada->docente->persona->apellidoPaterno . ' ' .
                    $claseActualizada->docente->persona->apellidoMaterno
                    : null
            ]
        ]);
    }

    /**
     * recuperar las calificaciones de una clase
     * @param int $idClase
     * @return JsonResponse
     */
    public function getCalificaciones(int $idClase): JsonResponse
    {
        // Verificar que la clase existe
        $clase = Clase::with([
            'asignatura:id,nombre,tipo',
            'grupoSemestre.semestre:id,numero',
            'grupoSemestre.grupo:id,prefijo',
            'docente.persona:id,nombre,apellidoPaterno,apellidoMaterno',
            'especialidad:id,nombre'
        ])->find($idClase);

        if (!$clase) {
            return response()->json([
                'message' => 'Clase no encontrada'
            ], 404);
        }

        // Obtener calificaciones con información del alumno
        $calificaciones = DB::table('calificacion as cal')
            ->join('alumno as a', 'cal.idAlumno', '=', 'a.id')
            ->join('persona as p', 'a.idPersona', '=', 'p.id')
            ->join('cuenta as c', 'p.idCuenta', '=', 'c.id')
            ->leftJoin('alumno_especialidad as aesp', 'a.id', '=', 'aesp.idAlumno')
            ->leftJoin('especialidad as esp', 'aesp.idEspecialidad', '=', 'esp.id')
            ->where('cal.idClase', $idClase)
            ->whereNull('p.deleted_at')
            ->whereNull('c.deleted_at')
            ->select(
                'cal.id as idCalificacion',
                'a.id as idAlumno',
                'a.nia',
                'p.nombre',
                'p.apellidoPaterno',
                'p.apellidoMaterno',
                DB::raw("CONCAT(p.apellidoPaterno, ' ', p.apellidoMaterno, ' ', p.nombre ) as nombreCompleto"),
                'esp.nombre as especialidad',
                'cal.momento1',
                'cal.momento2',
                'cal.momento3',
                DB::raw('ROUND((cal.momento1 + cal.momento2 + cal.momento3) / 3, 2) as promedio'),
                DB::raw("CASE
                    WHEN ROUND((cal.momento1 + cal.momento2 + cal.momento3) / 3, 2) >= 60 THEN 'APROBADO'
                    ELSE 'REPROBADO'
                END as estado")
            )
            ->orderBy('p.apellidoPaterno')
            ->orderBy('p.apellidoMaterno')
            ->orderBy('p.nombre')
            ->get();

        // Calcular estadísticas
        $estadisticas = [
            'totalAlumnos' => $calificaciones->count(),
            'aprobados' => $calificaciones->where('estado', 'APROBADO')->count(),
            'reprobados' => $calificaciones->where('estado', 'REPROBADO')->count(),
            'promedioGrupal' => $calificaciones->count() > 0
                ? round($calificaciones->avg('promedio'), 2)
                : 0,
            'momento1Promedio' => $calificaciones->count() > 0
                ? round($calificaciones->avg('momento1'), 2)
                : 0,
            'momento2Promedio' => $calificaciones->count() > 0
                ? round($calificaciones->avg('momento2'), 2)
                : 0,
            'momento3Promedio' => $calificaciones->count() > 0
                ? round($calificaciones->avg('momento3'), 2)
                : 0,
        ];

        return response()->json([
            'message' => 'Calificaciones recuperadas con éxito',
            'data' => [
                'clase' => [
                    'id' => $clase->id,
                    'anio' => $clase->anio,
                    'asignatura' => $clase->asignatura->nombre,
                    'tipoAsignatura' => $clase->asignatura->tipo,
                    'semestre' => $clase->grupoSemestre->semestre->numero,
                    'grupo' => $clase->grupoSemestre->grupo->prefijo,
                    'especialidad' => $clase->especialidad?->nombre,
                    'docente' => $clase->docente
                        ? $clase->docente->persona->nombre . ' ' .
                        $clase->docente->persona->apellidoPaterno . ' ' .
                        $clase->docente->persona->apellidoMaterno
                        : null
                ],
                'calificaciones' => $calificaciones,
                'estadisticas' => $estadisticas
            ]
        ]);
    }

    public function getExcelCalificaciones($idGrupoSemestre)
     {
         $grupoSemestre = GrupoSemestreInfoView::where('idGrupoSemestre', $idGrupoSemestre)->select(
             'semestre',
             'grupo',
         )->first();

         $dateNow = now()->format('Y-m-d');

         $fileName = 'CALIFICACIONES' . '_' . $grupoSemestre->semestre . $grupoSemestre->grupo . '_' . $dateNow . '.xlsx';

         return Excel::download(new CalificacionesExport($idGrupoSemestre), $fileName);
    }

    public function getExcelCalificacionesEsp($numeroSemestre, $idEspecialidad)
     {
         $dateNow = now()->format('Y-m-d');
         $especialidad = Especialidad::find($idEspecialidad);

         $fileName = 'CALIFICACIONES_' . 'ESP' . '_' . $especialidad->nombre . '_' . $numeroSemestre . '_' . $dateNow . '.xlsx';

         return Excel::download(
             new CalificacionesEspecialidadExport($idEspecialidad, $numeroSemestre),
             $fileName
         );
    }
}
