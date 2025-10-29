<?php

namespace App\Http\Controllers;

use App\Models\AlumnosGruposView;
use App\Models\Clase;
use App\Models\Especialidad;
use App\Models\GrupoSemestreInfoView;
use App\Models\VistaAsignatura;
use App\Models\VistaClasesGrupoSemestre;
use App\Models\VistaGruposSemestresAsignaturasAlumnosCalificacione;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GrupoSemestreInfoViewController extends Controller
{
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
            ->leftJoin('alumno_especialidad as aesp', 'a.id', '=', 'aesp.idAlumno')
            ->leftJoin('especialidad as esp', 'aesp.idEspecialidad', '=', 'esp.id')
            ->where('cal.idClase', $idClase)
            ->select(
                'cal.id as idCalificacion',
                'a.id as idAlumno',
                'a.nia',
                'p.nombre',
                'p.apellidoPaterno',
                'p.apellidoMaterno',
                DB::raw("CONCAT(p.nombre, ' ', p.apellidoPaterno, ' ', p.apellidoMaterno) as nombreCompleto"),
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

    public function getDetailsCalificationsByEspecialidad(int $id): JsonResponse
    {
        $especialidad = Especialidad::find($id);

        if (!$especialidad) {
            return response()->json([
                'message' => 'Especialidad no encontrada'
            ], 404);
        }

        $anioActual = now()->year;

        // Obtener clases de esta especialidad
        $clases = DB::table('clase as cl')
            ->join('grupo_semestre as gs', 'cl.idGrupoSemestre', '=', 'gs.id')
            ->join('semestre as s', 'gs.idSemestre', '=', 's.id')
            ->join('grupo as g', 'gs.idGrupo', '=', 'g.id')
            ->join('asignatura as a', 'cl.idAsignatura', '=', 'a.id')
            ->leftJoin('docente as d', 'cl.idDocente', '=', 'd.id')
            ->leftJoin('persona as p', 'd.idPersona', '=', 'p.id')
            ->where('cl.idEspecialidad', $id)
            ->where('cl.anio', $anioActual)
            ->select(
                'cl.id as idClase',
                'cl.anio',
                'gs.id as idGrupoSemestre',
                's.numero as semestre',
                'g.prefijo as grupo',
                'a.id as idAsignatura',
                'a.nombre as nombreAsignatura',
                'a.tipo as tipoAsignatura',
                'd.id as idDocente',
                DB::raw("CONCAT_WS(' ', p.nombre, p.apellidoPaterno, p.apellidoMaterno) as nombreDocente"),
                DB::raw("(SELECT COUNT(DISTINCT cal.idAlumno)
                         FROM calificacion cal
                         WHERE cal.idClase = cl.id) as alumnosInscritos")
            )
            ->orderBy('s.numero')
            ->orderBy('g.prefijo')
            ->orderBy('a.nombre')
            ->get();

        // Agrupar por semestre y grupo
        $clasesPorSemestreGrupo = $clases->groupBy(function($clase) {
            return $clase->semestre . '-' . $clase->grupo;
        })->map(function($grupo) {
            $primera = $grupo->first();
            return [
                'semestre' => $primera->semestre,
                'grupo' => $primera->grupo,
                'idGrupoSemestre' => $primera->idGrupoSemestre,
                'clases' => $grupo->map(function($clase) {
                    return [
                        'idClase' => $clase->idClase,
                        'anio' => $clase->anio,
                        'idAsignatura' => $clase->idAsignatura,
                        'nombreAsignatura' => $clase->nombreAsignatura,
                        'tipoAsignatura' => $clase->tipoAsignatura,
                        'idDocente' => $clase->idDocente,
                        'nombreDocente' => $clase->nombreDocente,
                        'alumnosInscritos' => $clase->alumnosInscritos
                    ];
                })->values()
            ];
        })->values();

        // Obtener alumnos inscritos en esta especialidad
        $alumnos = DB::table('alumno_especialidad as ae')
            ->join('alumno as a', 'ae.idAlumno', '=', 'a.id')
            ->join('persona as p', 'a.idPersona', '=', 'p.id')
            ->join('alumno_grupo_semestre as ags', 'a.id', '=', 'ags.idAlumno')
            ->join('grupo_semestre as gs', 'ags.idGrupoSemestre', '=', 'gs.id')
            ->join('semestre as s', 'gs.idSemestre', '=', 's.id')
            ->join('grupo as g', 'gs.idGrupo', '=', 'g.id')
            ->where('ae.idEspecialidad', $id)
            ->select(
                'a.id as idAlumno',
                'a.nia',
                'p.nombre',
                'p.apellidoPaterno',
                'p.apellidoMaterno',
                DB::raw("CONCAT(p.nombre, ' ', p.apellidoPaterno, ' ', p.apellidoMaterno) as nombreCompleto"),
                's.numero as semestre',
                'g.prefijo as grupo',
                'gs.id as idGrupoSemestre',
                'ae.semestreInicio'
            )
            ->orderBy('s.numero')
            ->orderBy('g.prefijo')
            ->orderBy('p.apellidoPaterno')
            ->get();

        // Agrupar alumnos por semestre-grupo
        $alumnosPorSemestreGrupo = $alumnos->groupBy(function($alumno) {
            return $alumno->semestre . '-' . $alumno->grupo;
        })->map(function($grupo) {
            return $grupo->map(function($alumno) {
                return [
                    'idAlumno' => $alumno->idAlumno,
                    'nia' => $alumno->nia,
                    'nombreCompleto' => $alumno->nombreCompleto,
                    'semestreInicio' => $alumno->semestreInicio
                ];
            })->values();
        });

        // Estadísticas generales
        $estadisticas = [
            'totalClases' => $clases->count(),
            'clasesConDocente' => $clases->whereNotNull('idDocente')->count(),
            'clasesSinDocente' => $clases->whereNull('idDocente')->count(),
            'totalAlumnos' => $alumnos->count(),
            'semestreGrupos' => $clasesPorSemestreGrupo->count()
        ];

        // Plan de estudios de la especialidad
        $planEstudios = DB::table('plan_asignatura as pa')
            ->join('asignatura as a', 'pa.idAsignatura', '=', 'a.id')
            ->join('semestre as s', 'pa.idSemestre', '=', 's.id')
            ->where('pa.idEspecialidad', $id)
            ->select(
                's.numero as semestre',
                'a.id as idAsignatura',
                'a.nombre as nombreAsignatura'
            )
            ->orderBy('s.numero')
            ->orderBy('a.nombre')
            ->get()
            ->groupBy('semestre');

        return response()->json([
            'message' => 'Información de especialidad recuperada con éxito',
            'data' => [
                'especialidad' => [
                    'id' => $especialidad->id,
                    'nombre' => $especialidad->nombre
                ],
                'anio' => $anioActual,
                'clasesPorSemestreGrupo' => $clasesPorSemestreGrupo,
                'alumnosPorSemestreGrupo' => $alumnosPorSemestreGrupo,
                'planEstudios' => $planEstudios,
                'estadisticas' => $estadisticas
            ]
        ]);
    }
}
