<?php
// app/Services/ClaseGeneratorService.php

namespace App\Services;

use App\Models\Alumno;
use App\Models\Calificacion;
use App\Models\Clase;
use App\Models\Especialidad;
use App\Models\GrupoSemestre;
use App\Models\PlanAsignatura;
use App\Models\Semestre;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClaseGeneratorService
{
    /**
     * Detecta los semestres activos según la fecha actual.
     */
    public function detectarSemestresActivos(): array
    {
        $hoy = Carbon::now();
        $semestres = Semestre::all();
        $semestresActivos = [];

        foreach ($semestres as $semestre) {
            if ($this->semestreEstaActivo($semestre, $hoy)) {
                $semestresActivos[] = $semestre->numero;
            }
        }

        return $semestresActivos;
    }

    /**
     * Determina si un semestre está activo según fechas exactas.
     */
    public function semestreEstaActivo($semestre, Carbon $hoy): bool
    {
        // Calcular año de inicio y fin
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
    }

    /**
     * Genera las clases y migra automáticamente a los alumnos.
     */
    public function generarClases(?array $semestresInput = null): array
    {
        DB::beginTransaction();

        try {
            $anio = Carbon::now()->year;

            // Detectar semestres activos si no se pasaron explícitamente
            $semestresActivos = $semestresInput ?: $this->detectarSemestresActivos();

            $totalClases = 0;
            $detalleClases = [];

            // Resultados de migración
            $alumnosMigrados = 0;
            $alumnosGraduados = 0;
            $detalleMigracion = [];

            // ============================================
            // PASO 0: GRADUAR ALUMNOS DE 6TO AL INICIO DE NUEVO CICLO
            // ============================================
            // Si estamos en semestres impares (1,3,5), graduamos a los de 6to
            // porque ya completaron el ciclo par (2,4,6)
            if (in_array(1, $semestresActivos)) {
                $graduacion = $this->graduarAlumnosDeSextoSemestre();
                $alumnosGraduados = $graduacion['total'];

                if ($graduacion['total'] > 0) {
                    $detalleMigracion[] = $graduacion;
                }
            }

            foreach ($semestresActivos as $numeroSemestre) {
                $semestre = Semestre::where('numero', $numeroSemestre)->first();
                if (!$semestre) continue;

                $gruposSemestre = GrupoSemestre::where('idSemestre', $semestre->id)
                    ->with('grupo')
                    ->get();

                foreach ($gruposSemestre as $gs) {
                    // ============================================
                    // 1. GENERAR CLASES (Tronco común)
                    // ============================================
                    $materiasTronco = PlanAsignatura::where('idSemestre', $semestre->id)
                        ->whereNull('idEspecialidad')
                        ->get();

                    foreach ($materiasTronco as $planAsignatura) {
                        $clase = Clase::firstOrCreate([
                            'idAsignatura' => $planAsignatura->idAsignatura,
                            'idGrupoSemestre' => $gs->id,
                            'idEspecialidad' => null,
                            'anio' => $anio
                        ]);
                        $totalClases++;
                    }

                    // ============================================
                    // 2. GENERAR CLASES (Especialidades)
                    // ============================================
                    if ($semestre->numero >= 3) {
                        foreach (Especialidad::pluck('id') as $idEspecialidad) {
                            $materiasEsp = PlanAsignatura::where('idSemestre', $semestre->id)
                                ->where('idEspecialidad', $idEspecialidad)
                                ->get();

                            foreach ($materiasEsp as $planAsignatura) {
                                Clase::firstOrCreate([
                                    'idAsignatura' => $planAsignatura->idAsignatura,
                                    'idGrupoSemestre' => $gs->id,
                                    'idEspecialidad' => $idEspecialidad,
                                    'anio' => $anio
                                ]);
                                $totalClases++;
                            }
                        }
                    }

                    $detalleClases[] = [
                        'semestre' => $semestre->numero,
                        'grupo' => $gs->grupo->prefijo,
                        'idGrupoSemestre' => $gs->id
                    ];

                    // ============================================
                    // 3. MIGRAR ALUMNOS DEL SEMESTRE ANTERIOR
                    // ============================================
                    if ($numeroSemestre > 1 && $numeroSemestre <= 6) {
                        $migracion = $this->migrarAlumnosAGrupoSemestre(
                            $numeroSemestre - 1, // semestre anterior
                            $gs,
                            $anio
                        );

                        if ($migracion['alumnosMigrados'] > 0) {
                            $alumnosMigrados += $migracion['alumnosMigrados'];
                            $detalleMigracion[] = $migracion;
                        }
                    }
                }
            }

            DB::commit();

            return [
                'success' => true,
                'anio' => $anio,
                'semestres' => $semestresActivos,
                'clases' => [
                    'total' => $totalClases,
                    'detalle' => $detalleClases
                ],
                'migracion' => [
                    'alumnosMigrados' => $alumnosMigrados,
                    'alumnosGraduados' => $alumnosGraduados,
                    'detalle' => $detalleMigracion
                ]
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al generar clases y migrar alumnos: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Migra alumnos del semestre anterior a un grupo-semestre específico.
     */
    private function migrarAlumnosAGrupoSemestre(int $semestreAnterior, GrupoSemestre $gsDestino, int $anio): array
    {
        // Buscar el grupo-semestre del semestre anterior con el mismo grupo
        $gsFuente = GrupoSemestre::whereHas('semestre', function ($q) use ($semestreAnterior) {
            $q->where('numero', $semestreAnterior);
        })
            ->where('idGrupo', $gsDestino->idGrupo)
            ->first();

        if (!$gsFuente) {
            return [
                'tipo' => 'migracion',
                'semestreFuente' => $semestreAnterior,
                'semestreDestino' => $gsDestino->semestre->numero,
                'grupo' => $gsDestino->grupo->prefijo,
                'alumnosMigrados' => 0,
                'alumnos' => []
            ];
        }

        // Obtener alumnos activos del grupo fuente (EXCLUYENDO ELIMINADOS)
        $alumnos = Alumno::whereHas('grupo_semestres', function ($q) use ($gsFuente) {
            $q->where('grupo_semestre.id', $gsFuente->id);
        })
            ->whereHas('persona', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->whereHas('persona.cuentum', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->where('situacion', 'ACTIVO')
            ->with(['persona' => function ($q) {
                $q->whereNull('deleted_at');
            }, 'especialidads'])
            ->get();

        $alumnosMigrados = [];

        foreach ($alumnos as $alumno) {
            // Validación adicional por si acaso
            if (!$alumno->persona || $alumno->persona->deleted_at) {
                continue;
            }

            // 1. Cambiar al nuevo grupo-semestre
            $alumno->grupo_semestres()->sync([$gsDestino->id]);

            // 2. Crear calificaciones en las nuevas clases
            $this->crearCalificacionesParaAlumno($alumno, $gsDestino, $anio);

            $alumnosMigrados[] = [
                'nia' => $alumno->nia,
                'nombre' => $alumno->persona->nombre . ' ' .
                    $alumno->persona->apellidoPaterno . ' ' .
                    $alumno->persona->apellidoMaterno
            ];
        }

        return [
            'tipo' => 'migracion',
            'semestreFuente' => $semestreAnterior,
            'semestreDestino' => $gsDestino->semestre->numero,
            'grupo' => $gsDestino->grupo->prefijo,
            'alumnosMigrados' => count($alumnosMigrados),
            'alumnos' => $alumnosMigrados
        ];
    }

    /**
     * Crea calificaciones para un alumno en las clases del grupo-semestre.
     */
    private function crearCalificacionesParaAlumno(Alumno $alumno, GrupoSemestre $gsDestino, int $anio): void
    {
        // Obtener especialidad del alumno
        $especialidad = $alumno->especialidads()->first();

        // Obtener clases disponibles para este grupo-semestre
        $clases = Clase::where('idGrupoSemestre', $gsDestino->id)
            ->where('anio', $anio)
            ->where(function ($query) use ($especialidad) {
                $query->whereNull('idEspecialidad'); // Tronco común

                if ($especialidad) {
                    $query->orWhere('idEspecialidad', $especialidad->id);
                }
            })
            ->get();

        // Crear calificaciones iniciales (0, 0, 0)
        foreach ($clases as $clase) {
            Calificacion::firstOrCreate([
                'idAlumno' => $alumno->id,
                'idClase' => $clase->id
            ], [
                'momento1' => 0,
                'momento2' => 0,
                'momento3' => 0
            ]);
        }
    }

    /**
     * Gradúa a los alumnos de 6to semestre al inicio de un nuevo ciclo.
     * Se ejecuta cuando comienza el ciclo impar (1,3,5).
     */
    private function graduarAlumnosDeSextoSemestre(): array
    {
        $alumnosGraduados = [];

        // Obtener alumnos ACTIVOS que están en 6to semestre
        $alumnos = Alumno::whereHas('grupo_semestres.semestre', function ($q) {
            $q->where('numero', 6);
        })
            ->whereHas('persona', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->whereHas('persona.cuentum', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->where('situacion', 'ACTIVO')
            ->with(['persona' => function ($q) {
                $q->whereNull('deleted_at');
            }])
            ->get();

        foreach ($alumnos as $alumno) {
            // Validación adicional
            if (!$alumno->persona || $alumno->persona->deleted_at) {
                continue;
            }

            // Cambiar situación a EGRESADO
            $alumno->update(['situacion' => 'EGRESADO']);
            // eliminar su cuenta y persona
            $alumno->persona->cuentum()->delete();
            $alumno->persona->delete();

            $alumnosGraduados[] = [
                'nia' => $alumno->nia,
                'nombre' => $alumno->persona->nombre . ' ' .
                    $alumno->persona->apellidoPaterno . ' ' .
                    $alumno->persona->apellidoMaterno
            ];
        }

        return [
            'tipo' => 'graduacion',
            'total' => count($alumnosGraduados),
            'alumnos' => $alumnosGraduados
        ];
    }
}
