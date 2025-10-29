<?php

namespace App\Services;

use App\Models\Clase;
use App\Models\Especialidad;
use App\Models\GrupoSemestre;
use App\Models\PlanAsignatura;
use App\Models\Semestre;
use Carbon\Carbon;

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

        // Solo generar si hoy está entre inicio y fin
        return $hoy->between($inicio, $fin);
    }

    /**
     * Genera las clases correspondientes para los semestres activos.
     */
    public function generarClases(?array $semestresInput = null): array
    {
        $anio = Carbon::now()->year;

        // Detectar semestres activos si no se pasaron explícitamente
        $semestresActivos = $semestresInput ?: $this->detectarSemestresActivos();

        $totalClases = 0;
        $detalle = [];

        foreach ($semestresActivos as $numeroSemestre) {
            $semestre = Semestre::where('numero', $numeroSemestre)->first();
            if (!$semestre) continue;

            $gruposSemestre = GrupoSemestre::where('idSemestre', $semestre->id)->get();

            foreach ($gruposSemestre as $gs) {
                // Materias troncales
                $materiasTronco = PlanAsignatura::where('idSemestre', $semestre->id)
                    ->whereNull('idEspecialidad')
                    ->get();

                foreach ($materiasTronco as $planAsignatura) {
                    Clase::firstOrCreate([
                        'idAsignatura' => $planAsignatura->idAsignatura,
                        'idGrupoSemestre' => $gs->id,
                        'idEspecialidad' => null,
                        'anio' => $anio
                    ]);
                    $totalClases++;
                }

                // Materias por especialidad (solo 3° o superior)
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

                $detalle[] = [
                    'semestre' => $semestre->numero,
                    'grupo' => $gs->grupo->prefijo,
                ];
            }
        }

        return [
            'anio' => $anio,
            'semestres' => $semestresActivos,
            'total' => $totalClases,
            'detalle' => $detalle,
        ];
    }
}
