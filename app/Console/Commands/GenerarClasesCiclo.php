<?php

namespace App\Console\Commands;

use App\Models\Clase;
use App\Models\Especialidad;
use App\Models\GrupoSemestre;
use App\Models\PlanAsignatura;
use App\Models\Semestre;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GenerarClasesCiclo extends Command
{
    // 🔹 Ya no pedimos el año como argumento
    protected $signature = 'clases:generar {--semestres=* : Lista de números de semestre a generar}';
    protected $description = 'Genera clases solo para los semestres activos o próximos a iniciar del ciclo actual';

    public function handle()
    {
        // 🔹 Obtener automáticamente el año actual
        $anio = Carbon::now()->year;

        $semestresInput = $this->option('semestres');

        // Si no se especifican semestres, detectar automáticamente cuáles están activos o próximos a iniciar
        if (empty($semestresInput)) {
            $semestresActivos = $this->detectarSemestresActivos();
        } else {
            $semestresActivos = $semestresInput;
        }

        if (empty($semestresActivos)) {
            $this->error('No se encontraron semestres activos o próximos a iniciar para la fecha actual');
            return 1;
        }

        $this->info("Generando clases para semestres: " . implode(', ', $semestresActivos) . " del ciclo {$anio}");

        $clasesCreadas = 0;

        foreach ($semestresActivos as $numeroSemestre) {
            $semestre = Semestre::where('numero', $numeroSemestre)->first();

            if (!$semestre) {
                $this->warn("Semestre {$numeroSemestre} no encontrado, omitiendo...");
                continue;
            }

            $gruposSemestre = GrupoSemestre::where('idSemestre', $semestre->id)->get();

            foreach ($gruposSemestre as $gs) {
                // Materias tronco común
                $materiasTroncoComun = PlanAsignatura::where('idSemestre', $semestre->id)
                    ->whereNull('idEspecialidad')
                    ->get();

                foreach ($materiasTroncoComun as $planAsignatura) {
                    Clase::firstOrCreate([
                        'idAsignatura' => $planAsignatura->idAsignatura,
                        'idGrupoSemestre' => $gs->id,
                        'idEspecialidad' => null,
                        'anio' => $anio
                    ], [
                        'idDocente' => null
                    ]);
                    $clasesCreadas++;
                }

                // Especialidades (solo si es 3er semestre o superior)
                if ($semestre->numero >= 3) {
                    $especialidades = Especialidad::pluck('id');

                    foreach ($especialidades as $idEspecialidad) {
                        $materiasEspecialidad = PlanAsignatura::where('idSemestre', $semestre->id)
                            ->where('idEspecialidad', $idEspecialidad)
                            ->get();

                        foreach ($materiasEspecialidad as $planAsignatura) {
                            Clase::firstOrCreate([
                                'idAsignatura' => $planAsignatura->idAsignatura,
                                'idGrupoSemestre' => $gs->id,
                                'idEspecialidad' => $idEspecialidad,
                                'anio' => $anio
                            ], [
                                'idDocente' => null
                            ]);
                            $clasesCreadas++;
                        }
                    }
                }

                $this->info("  ✓ Clases creadas para {$semestre->numero}° Sem - Grupo " . $gs->grupo->prefijo);
            }
        }

        $this->info("✓ Total: {$clasesCreadas} clases creadas");
        return 0;
    }

    /**
     * Detecta los semestres activos o que inician en el mes actual
     */
    private function detectarSemestresActivos()
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
     * Determina si el semestre está activo o puede generarse (mes de inicio)
     */
    private function semestreEstaActivo($semestre, $hoy)
    {
        // Calcular año de inicio y fin
        if ($semestre->mesFin < $semestre->mesInicio) {
            // Semestre cruza año
            $anioInicio = ($hoy->month >= $semestre->mesInicio) ? $hoy->year : $hoy->year - 1;
            $anioFin = $anioInicio + 1;
        } else {
            $anioInicio = $hoy->year;
            $anioFin = $anioInicio;
        }

        $inicio = Carbon::create($anioInicio, $semestre->mesInicio, $semestre->diaInicio);
        $fin = Carbon::create($anioFin, $semestre->mesFin, $semestre->diaFin);

        // Solo generar si hoy está entre inicio y fin
        if (! $hoy->between($inicio, $fin)) {
            return false;
        }

        // Alternancia par/impar
        $esImpar = $semestre->numero % 2 !== 0;

        // Puedes agregar lógica de preferencia si quieres solo pares o solo impares
        // Por ejemplo, si solo quieres pares en este momento:
        // return !$esImpar;

        return true; // si quieres que pares e impares se generen según fecha estricta
    }


}
