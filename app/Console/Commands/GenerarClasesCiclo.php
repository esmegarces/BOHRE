<?php

namespace App\Console\Commands;

use App\Models\Clase;
use App\Models\GrupoSemestre;
use App\Models\PlanAsignatura;
use App\Models\Semestre;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GenerarClasesCiclo extends Command
{
    protected $signature = 'clases:generar {anio} {--semestres=* : Lista de números de semestre a generar}';
    protected $description = 'Genera clases solo para los semestres activos del ciclo';

    public function handle()
    {
        $anio = $this->argument('anio');
        $semestresInput = $this->option('semestres');

        // Si no se especifican semestres, detectar automáticamente cuáles están activos
        if (empty($semestresInput)) {
            $semestresActivos = $this->detectarSemestresActivos();
        } else {
            $semestresActivos = $semestresInput;
        }

        if (empty($semestresActivos)) {
            $this->error('No se encontraron semestres activos para la fecha actual');
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
                    $especialidades = [1, 2, 3];

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

    private function semestreEstaActivo($semestre, $hoy)
    {
        $anioActual = $hoy->year;

        // Construir fechas de inicio y fin
        $inicio = Carbon::create($anioActual, $semestre->mesInicio, $semestre->diaInicio);

        // Si el mes de fin es menor al de inicio, el semestre cruza al año siguiente
        if ($semestre->mesFin < $semestre->mesInicio) {
            $fin = Carbon::create($anioActual + 1, $semestre->mesFin, $semestre->diaFin);
        } else {
            $fin = Carbon::create($anioActual, $semestre->mesFin, $semestre->diaFin);
        }

        return $hoy->between($inicio, $fin);
    }
}
