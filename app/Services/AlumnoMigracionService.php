<?php

// app/Services/AlumnoMigracionService.php

namespace App\Services;

use App\Models\Alumno;
use App\Models\AlumnoGrupoSemestre;
use App\Models\Calificacion;
use App\Models\Clase;
use App\Models\GrupoSemestre;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AlumnoMigracionService
{
    /**
     * Migra alumnos del semestre anterior al siguiente.
     * Solo migra alumnos activos.
     */
    public function migrarAlumnosASiguienteSemestre(?array $semestresFuente = null): array
    {
        DB::beginTransaction();

        try {
            $anioActual = Carbon::now()->year;
            $alumnosMigrados = 0;
            $alumnosGraduados = 0;
            $errores = [];
            $detalle = [];

            // Si no se especifican semestres fuente, detectar automáticamente
            if (!$semestresFuente) {
                $semestresFuente = $this->detectarSemestresAMigrar();
            }

            foreach ($semestresFuente as $semestreFuente) {
                // Determinar semestre destino
                $semestreDestino = $this->calcularSemestreDestino($semestreFuente);

                if ($semestreDestino === null) {
                    // Son alumnos de 6to semestre → graduarlos
                    $graduados = $this->graduarAlumnos($semestreFuente);
                    $alumnosGraduados += $graduados['total'];
                    $detalle[] = [
                        'tipo' => 'graduacion',
                        'semestreFuente' => $semestreFuente,
                        'alumnosGraduados' => $graduados['total'],
                        'alumnos' => $graduados['alumnos']
                    ];
                    continue;
                }

                // Migrar alumnos de semestre X a semestre X+1
                $migracion = $this->migrarAlumnosDeSemestre($semestreFuente, $semestreDestino, $anioActual);

                $alumnosMigrados += $migracion['total'];
                $detalle[] = [
                    'tipo' => 'migracion',
                    'semestreFuente' => $semestreFuente,
                    'semestreDestino' => $semestreDestino,
                    'alumnosMigrados' => $migracion['total'],
                    'grupos' => $migracion['grupos']
                ];
            }

            DB::commit();

            return [
                'success' => true,
                'anio' => $anioActual,
                'alumnosMigrados' => $alumnosMigrados,
                'alumnosGraduados' => $alumnosGraduados,
                'detalle' => $detalle,
                'errores' => $errores
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en migración de alumnos: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Detecta qué semestres deben migrarse.
     * Por ejemplo: si ahora inician 2, 4, 6 → los que vienen de 1, 3, 5
     */
    private function detectarSemestresAMigrar(): array
    {
        // Lógica: si es febrero, migramos pares (2,4,6)
        // Si es septiembre, migramos impares (1,3,5)
        $mesActual = Carbon::now()->month;

        if ($mesActual >= 9 || $mesActual <= 1) {
            // Ciclo sep-ene: inician 1, 3, 5 → migramos de ciclo anterior no aplicable
            // En realidad aquí NO hay migración porque es inicio de año escolar
            return [];
        } else {
            // Ciclo feb-jul: inician 2, 4, 6 → migramos 1→2, 3→4, 5→6
            return [1, 3, 5];
        }
    }

    /**
     * Calcula el semestre destino.
     * Si es 6, retorna null (graduación).
     */
    private function calcularSemestreDestino(int $semestreFuente): ?int
    {
        if ($semestreFuente >= 6) {
            return null; // Graduación
        }
        return $semestreFuente + 1;
    }

    /**
     * Migra alumnos de un semestre específico al siguiente.
     */
    private function migrarAlumnosDeSemestre(int $semestreFuente, int $semestreDestino, int $anio): array
    {
        $totalMigrados = 0;
        $gruposDetalle = [];

        // Obtener todos los grupos del semestre fuente
        $gruposSemestreFuente = GrupoSemestre::whereHas('semestre', function ($q) use ($semestreFuente) {
            $q->where('numero', $semestreFuente);
        })->with('grupo')->get();

        foreach ($gruposSemestreFuente as $gsFuente) {
            // Buscar el grupo-semestre destino (mismo grupo, siguiente semestre)
            $gsDestino = GrupoSemestre::whereHas('semestre', function ($q) use ($semestreDestino) {
                $q->where('numero', $semestreDestino);
            })
                ->where('idGrupo', $gsFuente->idGrupo)
                ->first();

            if (!$gsDestino) {
                Log::warning("No se encontró grupo-semestre destino para grupo {$gsFuente->grupo->prefijo}, semestre {$semestreDestino}");
                continue;
            }

            // Obtener alumnos activos del grupo fuente
            $alumnos = Alumno::whereHas('grupo_semestres', function ($q) use ($gsFuente) {
                $q->where('grupo_semestre.id', $gsFuente->id);
            })
                ->where('situacion', 'ACTIVO')
                ->get();

            $alumnosMigradosGrupo = [];

            foreach ($alumnos as $alumno) {
                // 1. Cambiar al nuevo grupo-semestre
                $alumno->grupo_semestres()->sync([$gsDestino->id]);

                // 2. Crear calificaciones en las nuevas clases
                $this->crearCalificacionesParaNuevaClases($alumno, $gsDestino, $anio);

                $alumnosMigradosGrupo[] = [
                    'nia' => $alumno->nia,
                    'nombre' => $alumno->persona->nombre . ' ' . $alumno->persona->apellidoPaterno
                ];

                $totalMigrados++;
            }

            $gruposDetalle[] = [
                'grupoFuente' => $gsFuente->grupo->prefijo,
                'grupoDestino' => $gsDestino->grupo->prefijo,
                'alumnosMigrados' => count($alumnosMigradosGrupo),
                'alumnos' => $alumnosMigradosGrupo
            ];
        }

        return [
            'total' => $totalMigrados,
            'grupos' => $gruposDetalle
        ];
    }

    /**
     * Crea calificaciones para un alumno en las clases del nuevo semestre.
     */
    private function crearCalificacionesParaNuevaClases(Alumno $alumno, GrupoSemestre $gsDestino, int $anio): void
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

        // Crear calificaciones iniciales
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
     * Gradúa a los alumnos de 6to semestre.
     */
    private function graduarAlumnos(int $semestre): array
    {
        if ($semestre !== 6) {
            return ['total' => 0, 'alumnos' => []];
        }

        $alumnosGraduados = [];

        // Obtener todos los alumnos de 6to semestre activos
        $alumnos = Alumno::whereHas('grupo_semestres.semestre', function ($q) {
            $q->where('numero', 6);
        })
            ->where('situacion', 'ACTIVO')
            ->with('persona')
            ->get();

        foreach ($alumnos as $alumno) {
            // Cambiar situación a EGRESADO
            $alumno->update(['situacion' => 'EGRESADO']);

            $alumnosGraduados[] = [
                'nia' => $alumno->nia,
                'nombre' => $alumno->persona->nombre . ' ' .
                    $alumno->persona->apellidoPaterno . ' ' .
                    $alumno->persona->apellidoMaterno
            ];
        }

        return [
            'total' => count($alumnosGraduados),
            'alumnos' => $alumnosGraduados
        ];
    }

    /**
     * Obtiene reporte previo de qué se migrará (sin ejecutar).
     */
    public function previsualizarMigracion(?array $semestresFuente = null): array
    {
        if (!$semestresFuente) {
            $semestresFuente = $this->detectarSemestresAMigrar();
        }

        $preview = [];

        foreach ($semestresFuente as $semestreFuente) {
            $semestreDestino = $this->calcularSemestreDestino($semestreFuente);

            if ($semestreDestino === null) {
                // Contar alumnos a graduar
                $total = Alumno::whereHas('grupo_semestres.semestre', function ($q) {
                    $q->where('numero', 6);
                })
                    ->where('situacion', 'ACTIVO')
                    ->count();

                $preview[] = [
                    'tipo' => 'graduacion',
                    'semestreFuente' => $semestreFuente,
                    'totalAlumnos' => $total
                ];
            } else {
                // Contar alumnos a migrar
                $total = Alumno::whereHas('grupo_semestres.semestre', function ($q) use ($semestreFuente) {
                    $q->where('numero', $semestreFuente);
                })
                    ->where('situacion', 'ACTIVO')
                    ->count();

                $preview[] = [
                    'tipo' => 'migracion',
                    'semestreFuente' => $semestreFuente,
                    'semestreDestino' => $semestreDestino,
                    'totalAlumnos' => $total
                ];
            }
        }

        return [
            'semestresFuente' => $semestresFuente,
            'acciones' => $preview
        ];
    }
}
