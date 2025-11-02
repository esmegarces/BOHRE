<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Docente;
use App\Models\GrupoSemestre;
use App\Models\Clase;
use App\Models\Calificacion;
use App\Models\Semestre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function obtenerEstadisticas()
    {
        try {
            $anioActual = Carbon::now()->year;

            // ============================================
            // 1. ESTADÍSTICAS GENERALES
            // ============================================
            $totalAlumnos = Alumno::whereHas('persona', function ($q) {
                $q->whereNull('deleted_at');
            })->count();

            $alumnosActivos = Alumno::where('situacion', 'ACTIVO')
                ->whereHas('persona', function ($q) {
                    $q->whereNull('deleted_at');
                })->count();

            $alumnosEgresados = Alumno::where('situacion', 'EGRESADO')
                ->whereHas('persona', function ($q) {
                    $q->whereNull('deleted_at');
                })->count();

            $totalDocentes = Docente::whereHas('persona', function ($q) {
                $q->whereNull('deleted_at');
            })->count();

            // ============================================
            // 1.5. DISTRIBUCIÓN POR SEXO
            // ============================================

            // Alumnos por sexo
            $alumnosPorSexo = Alumno::join('persona', 'alumno.idPersona', '=', 'persona.id')
                ->whereNull('persona.deleted_at')
                ->where('alumno.situacion', 'ACTIVO')
                ->select('persona.sexo', DB::raw('COUNT(*) as total'))
                ->groupBy('persona.sexo')
                ->get()
                ->map(function ($item) {
                    return [
                        'sexo' => $item->sexo === 'M' ? 'Masculino' : 'Femenino',
                        'total' => $item->total
                    ];
                });

            // Docentes por sexo
            $docentesPorSexo = Docente::join('persona', 'docente.idPersona', '=', 'persona.id')
                ->whereNull('persona.deleted_at')
                ->select('persona.sexo', DB::raw('COUNT(*) as total'))
                ->groupBy('persona.sexo')
                ->get()
                ->map(function ($item) {
                    return [
                        'sexo' => $item->sexo === 'M' ? 'Masculino' : 'Femenino',
                        'total' => $item->total
                    ];
                });

            // ============================================
            // 2. ALUMNOS POR SEMESTRE
            // ============================================
            $alumnosPorSemestre = GrupoSemestre::with('semestre')
                ->get()
                ->map(function ($gs) {
                    $total = Alumno::whereHas('grupo_semestres', function ($q) use ($gs) {
                        $q->where('grupo_semestre.id', $gs->id);
                    })
                        ->whereHas('persona', function ($q) {
                            $q->whereNull('deleted_at');
                        })
                        ->where('situacion', 'ACTIVO')
                        ->count();

                    return [
                        'semestre' => $gs->semestre->numero,
                        'total' => $total
                    ];
                })
                ->groupBy('semestre')
                ->map(function ($grupo) {
                    return $grupo->sum('total');
                })
                ->sortKeys()
                ->map(function ($total, $semestre) {
                    return [
                        'semestre' => "Semestre {$semestre}",
                        'total' => $total
                    ];
                })
                ->values();

            // ============================================
            // 3. ALUMNOS POR ESPECIALIDAD
            // ============================================
            $alumnosPorEspecialidad = DB::table('alumno')
                ->join('alumno_especialidad', 'alumno.id', '=', 'alumno_especialidad.idAlumno')
                ->join('especialidad', 'alumno_especialidad.idEspecialidad', '=', 'especialidad.id')
                ->join('persona', 'alumno.idPersona', '=', 'persona.id')
                ->whereNull('persona.deleted_at')
                ->where('alumno.situacion', 'ACTIVO')
                ->select('especialidad.nombre', DB::raw('COUNT(*) as total'))
                ->groupBy('especialidad.nombre')
                ->get()
                ->map(function ($item) {
                    return [
                        'especialidad' => $item->nombre,
                        'total' => $item->total
                    ];
                });

            // ============================================
            // 4. ALUMNOS POR GRUPO
            // ============================================
            $alumnosPorGrupo = GrupoSemestre::with(['grupo', 'semestre'])
                ->get()
                ->map(function ($gs) {
                    $total = Alumno::whereHas('grupo_semestres', function ($q) use ($gs) {
                        $q->where('grupo_semestre.id', $gs->id);
                    })
                        ->whereHas('persona', function ($q) {
                            $q->whereNull('deleted_at');
                        })
                        ->where('situacion', 'ACTIVO')
                        ->count();

                    return [
                        'grupo' => $gs->grupo->prefijo . '-' . $gs->semestre->numero,
                        'total' => $total
                    ];
                })
                ->filter(function ($item) {
                    return $item['total'] > 0;
                })
                ->sortByDesc('total')
                ->take(10)
                ->values();

            // ============================================
            // 5. PROMEDIO GENERAL DE CALIFICACIONES
            // ============================================
            $promedioGeneral = Calificacion::whereHas('clase', function ($q) use ($anioActual) {
                $q->where('anio', $anioActual);
            })
                ->whereHas('alumno.persona', function ($q) {
                    $q->whereNull('deleted_at');
                })
                ->selectRaw('AVG((momento1 + momento2 + momento3) / 3) as promedio')
                ->first()
                ->promedio ?? 0;

            // ============================================
            // 6. DISTRIBUCIÓN DE CALIFICACIONES
            // ============================================
            $distribucionCalificaciones = Calificacion::whereHas('clase', function ($q) use ($anioActual) {
                $q->where('anio', $anioActual);
            })
                ->whereHas('alumno.persona', function ($q) {
                    $q->whereNull('deleted_at');
                })
                ->get()
                ->map(function ($cal) {
                    $promedio = ($cal->momento1 + $cal->momento2 + $cal->momento3) / 3;
                    if ($promedio >= 9) return 'Excelente (9-10)';
                    if ($promedio >= 8) return 'Bien (8-8.9)';
                    if ($promedio >= 7) return 'Regular (7-7.9)';
                    if ($promedio >= 6) return 'Suficiente (6-6.9)';
                    return 'Reprobado (0-5.9)';
                })
                ->groupBy(function ($item) {
                    return $item;
                })
                ->map(function ($grupo, $rango) {
                    return [
                        'rango' => $rango,
                        'total' => $grupo->count()
                    ];
                })
                ->sortByDesc('total')
                ->values();

            // ============================================
            // 7. CLASES ACTIVAS DEL AÑO
            // ============================================
            $clasesActivas = Clase::where('anio', $anioActual)->count();

            // ============================================
            // 8. SEMESTRES ACTIVOS
            // ============================================
            $hoy = Carbon::now();
            $semestresActivos = Semestre::all()->filter(function ($semestre) use ($hoy) {
                if ($semestre->mesFin < $semestre->mesInicio) {
                    $anioInicio = ($hoy->month >= $semestre->mesInicio) ? $hoy->year : $hoy->year - 1;
                    $anioFin = $anioInicio + 1;
                } else {
                    $anioInicio = $hoy->year;
                    $anioFin = $anioInicio;
                }

                $inicio = Carbon::create($anioInicio, $semestre->mesInicio, $semestre->diaInicio);
                $fin = Carbon::create($anioFin, $semestre->mesFin, $semestre->diaFin);

                return $hoy->between($inicio, $fin);
            })->pluck('numero')->values();

            return response()->json([
                'success' => true,
                'data' => [
                    'general' => [
                        'totalAlumnos' => $totalAlumnos,
                        'alumnosActivos' => $alumnosActivos,
                        'alumnosEgresados' => $alumnosEgresados,
                        'totalDocentes' => $totalDocentes,
                        'clasesActivas' => $clasesActivas,
                        'promedioGeneral' => round($promedioGeneral, 2),
                        'semestresActivos' => $semestresActivos
                    ],
                    'graficos' => [
                        'alumnosPorSemestre' => $alumnosPorSemestre,
                        'alumnosPorEspecialidad' => $alumnosPorEspecialidad,
                        'alumnosPorGrupo' => $alumnosPorGrupo,
                        'distribucionCalificaciones' => $distribucionCalificaciones,
                        'alumnosPorSexo' => $alumnosPorSexo,
                        'docentesPorSexo' => $docentesPorSexo
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }
}
