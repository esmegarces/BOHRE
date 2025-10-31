<?php

namespace Database\Seeders;

use App\Models\Alumno;
use App\Models\CicloEscolar;
use App\Models\Clase;
use App\Models\Cuentum;
use App\Models\Direccion;
use App\Models\Docente;
use App\Models\Especialidad;
use App\Models\Generacion;
use App\Models\GrupoSemestre;
use App\Models\Localidad;
use App\Models\Persona;
use App\Models\PlanAsignatura;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CuentaFactorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // mostrando en consola que se esta ejecutando el seeder
        $this->command->info('🌱 Ejecutando el seeder de cuentas con sus relaciones...');

        \DB::transaction(function () {
            // Verificar que existan localidades en la base de datos antes de continuar
            // Las localidades son necesarias para crear direcciones válidas
            if (Localidad::count() === 0) {
                $this->command->warn('⚠️ No hay localidades registradas, crea algunas antes de ejecutar el seeder.');
                return;
            }

            // Generar 10 cuentas de usuario con sus relaciones completas
            Cuentum::factory(100)
                ->create()
                ->each(function ($cuenta) {
                    // Crear una dirección aleatoria vinculada a una localidad existente
                    $direccion = Direccion::factory()->create();

                    // Crear persona relacionada tanto a la cuenta como a la dirección
                    // Cada persona está vinculada a una cuenta (login) y tiene una dirección física
                    $persona = Persona::factory()
                        ->for($cuenta, 'cuentum')      // Relacionar con la cuenta creada
                        ->for($direccion, 'direccion')  // Relacionar con la dirección creada
                        ->create();

                    // Verificar si la cuenta es de tipo 'alumno'
                    if ($cuenta->rol === 'alumno') {

                        // Crear el registro de alumno relacionado a la persona
                        $alumno = Alumno::factory()
                            ->for($persona, 'persona')
                            ->create();

                        $gsRow = GrupoSemestre::join('grupo as g', 'grupo_semestre.idGrupo', '=', 'g.id')
                            ->join('semestre as s', 'grupo_semestre.idSemestre', '=', 's.id')
                            ->whereRaw("
                                        CURDATE() BETWEEN
                                            CAST(CONCAT(
                                                YEAR(CURDATE()),
                                                '-',
                                                LPAD(s.mesInicio, 2, '0'),
                                                '-',
                                                LPAD(s.diaInicio, 2, '0')
                                            ) AS DATE)
                                        AND
                                            CAST(CONCAT(
                                                (CASE
                                                    WHEN s.mesFin < s.mesInicio
                                                    THEN YEAR(CURDATE()) + 1
                                                    ELSE YEAR(CURDATE())
                                                END),
                                                '-',
                                                LPAD(s.mesFin, 2, '0'),
                                                '-',
                                                LPAD(s.diaFin, 2, '0')
                                            ) AS DATE)
                                    ")
                            ->inRandomOrder()
                            ->select('grupo_semestre.id')
                            ->first();

                        if (!$gsRow) {
                            $this->command->warn('⚠️ No hay grupo_semestre activo en la fecha actual.');
                            return;
                        }

                        $grupoSemestre = GrupoSemestre::with('semestre', 'grupo')->find($gsRow->id);

                        $numeroSemestre = $grupoSemestre->semestre->numero;

                        // Determinar la generación según el semestre
                        if (in_array($numeroSemestre, [1, 2])) {
                            $fechaInicio = '2025-08-01';
                            $fechaFin = '2028-07-31';
                        } elseif (in_array($numeroSemestre, [3, 4])) {
                            $fechaInicio = '2024-08-01';
                            $fechaFin = '2027-07-31';
                        } elseif (in_array($numeroSemestre, [5, 6])) {
                            $fechaInicio = '2023-08-01';
                            $fechaFin = '2026-07-31';
                        }

                        if ($fechaInicio && $fechaFin) {
                            // Buscar esa generación en la BD
                            $generacion = Generacion::where(['fechaIngreso' => $fechaInicio, 'fechaEgreso' => $fechaFin])->first();

                            if ($generacion) {
                                // Asociar alumno con generación y semestre inicial
                                $generacion->alumnos()->syncWithoutDetaching([
                                    $alumno->id => ['semestreInicial' => $numeroSemestre],
                                ]);
                            } else {
                                \Log::warning("No se encontró la generación {$fechaInicio}-{$fechaFin}.}");
                            }
                        }

                        // Asignar el alumno al grupo-semestre en la tabla pivot
                        $grupoSemestre->alumnos()->syncWithoutDetaching([$alumno->id]);

                        // Inicializar el arreglo que contendrá todas las asignaturas del alumno
                        $asignaturasTotales = [];
                        $especialidad = null;

                        $planAsignaturasGenerales = DB::table('plan_asignatura')
                            ->join('asignatura as asi', 'plan_asignatura.idAsignatura', '=', 'asi.id')
                            ->whereNull('plan_asignatura.idEspecialidad')
                            ->where('plan_asignatura.idSemestre', $grupoSemestre->semestre->id)
                            ->select('asi.*')
                            ->get()
                            ->toArray();

                        // guardar las asignaturas del tronco común
                        $asignaturasTotales = $planAsignaturasGenerales;

                        // A partir del 3er semestre, los alumnos eligen una especialidad
                        // (Alimentos, Administración o Salud) y cursan materias específicas
                        if ($grupoSemestre->semestre->numero >= 3) {

                            // Seleccionar una especialidad aleatoria
                            $especialidad = Especialidad::inRandomOrder()->first();

                            // Registrar la especialidad del alumno con el semestre en que inició
                            $alumno->especialidads()->syncWithoutDetaching([$especialidad->id => [
                                'semestreInicio' => $grupoSemestre->semestre->numero
                            ]
                            ]);

                            // Obtener las asignaturas específicas de la especialidad para el semestre actual del alumno
                            $planAsignaturas = $especialidad->plan_asignaturas()
                                ->with('asignatura')
                                ->where('idSemestre', $grupoSemestre->semestre->id)
                                ->get()
                                ->map(function ($item) {
                                    return $item->asignatura;
                                })
                                ->toArray();

                            // Combinar las asignaturas del tronco común con las de especialidad
                            // Resultado: materias generales + materias de especialidad
                            $asignaturasTotales = array_merge($asignaturasTotales, $planAsignaturas);
                        }

                        // Recorrer todas las asignaturas (tronco común + especialidad si aplica) y crear un registro de clase por cada una
                        foreach ($asignaturasTotales as $asignatura) {
                            // Obtener el ID de la asignatura (puede venir como array u objeto)
                            $asigId = is_array($asignatura) ? $asignatura['id'] : $asignatura->id;

                            // Buscar el plan de asignatura para obtener si pertenece a una especialidad
                            // Esto es importante para diferenciar materias comunes de las de especialidad
                            $plan = PlanAsignatura::where('idAsignatura', $asigId)
                                ->where('idSemestre', $grupoSemestre->semestre->id)
                                ->first();

                            // Si el plan existe y tiene idEspecilidad, guardar ese ID
                            // Si es NULL, significa que es una materia del tronco común
                            $idEspecialidad = $plan ? $plan->idEspecialidad : null;

                            // Crear el registro de clase con toda la información:
                            $clase = Clase::firstOrCreate(
                                [
                                    'idAsignatura' => $asigId,
                                    'idGrupoSemestre' => $grupoSemestre->id,
                                    'idEspecialidad' => $idEspecialidad,
                                    'anio' => Carbon::now()->year, // 👈 incluir aquí también
                                ]
                            );


                            $alumno->calificacions()->firstOrCreate(
                                [
                                    'idClase' => $clase->id,
                                ],
                                [
                                    'momento1' => 0,
                                    'momento2' => 0,
                                    'momento3' => 0,
                                ]
                            );

                        }

                    } else {
                        // Si la cuenta es de tipo 'docente' o 'admin',
                        // crear solo el registro de docente sin asignar clases
                        Docente::factory()
                            ->for($persona, 'persona')
                            ->create();
                    }
                });
        });

        $this->command->info('🌱 Todo bien...');
    }
}
