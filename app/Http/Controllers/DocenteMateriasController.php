<?php

namespace App\Http\Controllers;

use App\Models\Calificacion;
use App\Models\Docente;
use App\Models\Clase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocenteMateriasController extends Controller
{
    /**
     * Obtiene las materias que imparte un docente agrupadas por tipo
     *
     * @param int $idPerson
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerMateriasPorDocente($idPerson)
    {
        try {
            // Verificar que el docente existe
            $docente = Docente::with('persona')
                ->where('idPersona', $idPerson)
                ->firstOrFail();


            $anioActual = now()->year;

            // ============================================
            // 1. MATERIAS COMUNES (sin especialidad)
            // Agrupadas por Semestre - Grupo
            // ============================================
            $materiasComunes = Clase::where('anio', $anioActual)
                ->whereNull('idEspecialidad') // Solo materias comunes
                ->where('idDocente', $docente->id)
                ->with([
                    'asignatura',
                    'grupoSemestre.grupo',
                    'grupoSemestre.semestre'
                ])
                ->get()
                ->groupBy(function ($clase) {
                    return $clase->grupoSemestre->semestre->numero . '-' . $clase->grupoSemestre->grupo->prefijo;
                })
                ->map(function ($clases, $key) {
                    $primeraClase = $clases->first();
                    [$semestre, $grupo] = explode('-', $key);

                    return [
                        'semestre' => (int)$semestre,
                        'grupo' => $grupo,
                        'grupoSemestre' => $grupo . '-' . $semestre,
                        'idGrupoSemestre' => $primeraClase->grupoSemestre->id,
                        'materias' => $clases->map(function ($clase) {
                            return [
                                'idClase' => $clase->id,
                                'idAsignatura' => $clase->idAsignatura,
                                'nombreAsignatura' => $clase->asignatura->nombre,
                                'totalAlumnos' => $clase->calificacions()->count()
                            ];
                        })->values()
                    ];
                })
                ->sortBy('semestre')
                ->values();

            // ============================================
            // 2. MATERIAS DE ESPECIALIDAD
            // Agrupadas por Especialidad y Semestre
            // ============================================
            $materiasEspecialidad = Clase::where('anio', $anioActual)
                ->whereNotNull('idEspecialidad') // Solo materias de especialidad
                ->where('idDocente', $docente->id)
                ->with([
                    'asignatura',
                    'especialidad',
                    'grupoSemestre.semestre',
                    'grupoSemestre.grupo'
                ])
                ->get()
                ->groupBy(function ($clase) {
                    return $clase->especialidad->nombre . '|' . $clase->grupoSemestre->semestre->numero;
                })
                ->map(function ($clases, $key) {
                    $primeraClase = $clases->first();
                    [$especialidad, $semestre] = explode('|', $key);

                    return [
                        'especialidad' => $especialidad,
                        'idEspecialidad' => $primeraClase->idEspecialidad,
                        'semestre' => (int)$semestre,
                        'grupos' => $clases->groupBy('idGrupoSemestre')->map(function ($clasesGrupo) {
                            $primera = $clasesGrupo->first();
                            return [
                                'idGrupoSemestre' => $primera->idGrupoSemestre,
                                'grupo' => $primera->grupoSemestre->grupo->prefijo,
                                'materias' => $clasesGrupo->map(function ($clase) {
                                    return [
                                        'idClase' => $clase->id,
                                        'idAsignatura' => $clase->idAsignatura,
                                        'nombreAsignatura' => $clase->asignatura->nombre,
                                        'totalAlumnos' => $clase->calificacions()->count()
                                    ];
                                })->values()
                            ];
                        })->values()
                    ];
                })
                ->sortBy('semestre')
                ->values();

            // ============================================
            // 3. ESTADÍSTICAS DEL DOCENTE
            // ============================================
            $totalClases = Clase::where('anio', $anioActual)
                ->where('idDocente', $idPerson)
                ->count();

            $totalAlumnos = DB::table('calificacion')
                ->join('clase', 'calificacion.idClase', '=', 'clase.id')
                ->where('clase.idDocente', $idPerson)
                ->where('clase.anio', $anioActual)
                ->distinct('calificacion.idAlumno')
                ->count('calificacion.idAlumno');

            $materiasUnicas = Clase::where('anio', $anioActual)
                ->where('idDocente', $idPerson)
                ->distinct('idAsignatura')
                ->count('idAsignatura');

            return response()->json([
                'success' => true,
                'data' => [
                    'docente' => [
                        'id' => $docente->id,
                        'nombre' => $docente->persona->nombre . ' ' .
                            $docente->persona->apellidoPaterno . ' ' .
                            $docente->persona->apellidoMaterno
                    ],
                    'estadisticas' => [
                        'totalClases' => $totalClases,
                        'totalAlumnos' => $totalAlumnos,
                        'materiasUnicas' => $materiasUnicas,
                        'totalComunes' => $materiasComunes->count(),
                        'totalEspecialidad' => $materiasEspecialidad->count()
                    ],
                    'materiasComunes' => $materiasComunes,
                    'materiasEspecialidad' => $materiasEspecialidad
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Docente no encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener materias: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene el detalle de una clase específica con sus alumnos
     *
     * @param int $idClase
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerDetalleClase($idClase)
    {
        try {
            $clase = Clase::with([
                'asignatura',
                'grupoSemestre.grupo',
                'grupoSemestre.semestre',
                'especialidad',
                'calificacions.alumno.persona'
            ])->findOrFail($idClase);

            $alumnos = $clase->calificacions->map(function ($calificacion) {
                $persona = $calificacion->alumno->persona;
                return [
                    'idAlumno' => $calificacion->idAlumno,
                    'nia' => $calificacion->alumno->nia,
                    'nombre' => $persona->nombre . ' ' . $persona->apellidoPaterno . ' ' . $persona->apellidoMaterno,
                    'momento1' => $calificacion->momento1,
                    'momento2' => $calificacion->momento2,
                    'momento3' => $calificacion->momento3,
                    'promedio' => round(
                        ($calificacion->momento1 + $calificacion->momento2 + $calificacion->momento3) / 3,
                        2
                    ),
                ];
            })->sortBy([
                fn($alumno) => $alumno['nombre'] ? explode(' ', $alumno['nombre'])[1] : '', // Apellido Paterno
                fn($alumno) => $alumno['nombre'] ? explode(' ', $alumno['nombre'])[2] : '', // Apellido Materno
                fn($alumno) => $alumno['nombre'] ? explode(' ', $alumno['nombre'])[0] : '', // Nombre
            ])->values();


            return response()->json([
                'success' => true,
                'data' => [
                    'clase' => [
                        'id' => $clase->id,
                        'asignatura' => $clase->asignatura->nombre,
                        'grupo' => $clase->grupoSemestre->grupo->prefijo,
                        'semestre' => $clase->grupoSemestre->semestre->numero,
                        'especialidad' => $clase->especialidad ? $clase->especialidad->nombre : null,
                        'anio' => $clase->anio
                    ],
                    'alumnos' => $alumnos,
                    'estadisticas' => [
                        'totalAlumnos' => $alumnos->count(),
                        'promedioGrupo' => round($alumnos->avg('promedio'), 2),
                        'aprobados' => $alumnos->filter(fn($a) => $a['promedio'] >= 6)->count(),
                        'reprobados' => $alumnos->filter(fn($a) => $a['promedio'] < 6)->count()
                    ]
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Clase no encontrada'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener detalle de clase: ' . $e->getMessage()
            ], 500);
        }
    }

    // En tu controller
    public function guardarCalificaciones(Request $request, $idClase)
    {
        $request->validate([
            'calificaciones' => 'required|array',
            'calificaciones.*.idAlumno' => 'required|integer',
            'calificaciones.*.momento1' => 'numeric|min:0|max:10',
            'calificaciones.*.momento2' => 'numeric|min:0|max:10',
            'calificaciones.*.momento3' => 'numeric|min:0|max:10',
        ]);

        try {
            foreach ($request->calificaciones as $calif) {
                $calificacion = Calificacion::where('idAlumno', $calif['idAlumno'])
                    ->where('idClase', $idClase)
                    ->first();

                if (!$calificacion) {
                    throw new \Exception('Calificación no encontrada para alumno ' . $calif['idAlumno']);
                }

                $calificacion->update([
                    'momento1' => $calif['momento1'],
                    'momento2' => $calif['momento2'],
                    'momento3' => $calif['momento3']
                ]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar calificaciones: ' . $e->getMessage()
            ], 500);
        }
    }

}
