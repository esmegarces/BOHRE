<?php

namespace App\Http\Controllers;

use App\Models\AsignaturasEspecialidadesView;
use App\Models\Especialidad;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class EspecialidadesController extends Controller
{
    /**
     * obtener todas las especialidades
     * @return JsonResponse
     */
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

    /**
     * crear nueva especialidad
     * @param Request $request
     * @return JsonResponse
     */
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

    /**
     * actualizar especialidad
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
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

    /**
     * obtener asignaturas por especialidad
     * @param int $id
     * @return JsonResponse
     */
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


    /**
     * obtener detalles por especialidad, alumnos, clases y calificaciones
     * @param int $id
     * @return JsonResponse
     */
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
        $clasesPorSemestreGrupo = $clases->groupBy(function ($clase) {
            return $clase->semestre . '-' . $clase->grupo;
        })->map(function ($grupo) {
            $primera = $grupo->first();
            return [
                'semestre' => $primera->semestre,
                'grupo' => $primera->grupo,
                'idGrupoSemestre' => $primera->idGrupoSemestre,
                'clases' => $grupo->map(function ($clase) {
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
            ->join('cuenta as c', 'p.idCuenta', '=', 'c.id')
            ->join('alumno_grupo_semestre as ags', 'a.id', '=', 'ags.idAlumno')
            ->join('grupo_semestre as gs', 'ags.idGrupoSemestre', '=', 'gs.id')
            ->join('semestre as s', 'gs.idSemestre', '=', 's.id')
            ->join('grupo as g', 'gs.idGrupo', '=', 'g.id')
            ->where('ae.idEspecialidad', $id)
            ->whereNull('c.deleted_at')
            ->whereNull('p.deleted_at')
            ->where('a.situacion', 'ACTIVO')
            ->select(
                'p.id as idAlumno',
                'a.nia',
                'p.nombre',
                'p.apellidoPaterno',
                'p.apellidoMaterno',
                DB::raw("CONCAT(p.apellidoPaterno, ' ', p.apellidoMaterno, ' ' ,p.nombre) as nombreCompleto"),
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
        $alumnosPorSemestreGrupo = $alumnos->groupBy(function ($alumno) {
            return $alumno->semestre . '-' . $alumno->grupo;
        })->map(function ($grupo) {
            return $grupo->map(function ($alumno) {
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
