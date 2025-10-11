<?php

namespace Database\Seeders;

use App\Models\Alumno;
use App\Models\CicloEscolar;
use App\Models\Cuentum;
use App\Models\Direccion;
use App\Models\Docente;
use App\Models\Especialidad;
use App\Models\Generacion;
use App\Models\GrupoSemestre;
use App\Models\Localidad;
use App\Models\Persona;
use App\Models\PlanAsignatura;
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
            Cuentum::factory(10)
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

                        // Seleccionar aleatoriamente un grupo-semestre existente Ejemplo: 1-A, 2-B, 3-C, etc.
                        $grupoSemestre = GrupoSemestre::inRandomOrder()->first();

                        // Seleccionar aleatoriamente una generación existente Ejemplo: 2019-2022, 2020-2023, etc.
                        $generacion = Generacion::inRandomOrder()->first();

                        // Obtener un ciclo escolar que esté dentro del rango de años de la generación
                        // Esto asegura que el ciclo escolar sea coherente con la generación del alumno
                        $cicloEscolar = CicloEscolar::where('anioInicio', '>=', $generacion->anioIngreso->year)
                            ->where('anioFin', '<=', $generacion->anioEgreso->year)
                            ->inRandomOrder()
                            ->first();

                        // Registrar la relación alumno-generación en la tabla pivot
                        // El semestre inicial es el número del semestre del grupo asignado
                        $generacion->alumnos()->attach($alumno->id, [
                            'semestreInicial' => $grupoSemestre->semestre->numero,
                        ]);

                        // Registrar en qué semestre está cursando el alumno en este ciclo escolar
                        // Esto permite el historial de semestres cursados por ciclo
                        $cicloEscolar->alumno_ciclos()->create([
                            'idAlumno' => $alumno->id,
                            'semestreCursado' => $grupoSemestre->semestre->numero,
                        ]);

                        // Asignar el alumno al grupo-semestre en la tabla pivot
                        // Esto vincula al alumno con su grupo específico (ej: 3-A)
                        $grupoSemestre->alumnos()->attach($alumno->id);

                        // Buscar un docente existente de forma aleatoria para asignar las clases
                        $docente = Docente::inRandomOrder()->first();

                        // Si no existe ningún docente, crear uno nuevo con todas sus relaciones
                        if (!$docente) {
                            $docente = Docente::factory()
                                ->for(
                                    Persona::factory()
                                        ->for(Cuentum::factory())
                                        ->for(Direccion::factory()),
                                    'persona'
                                )
                                ->create();
                        }

                        // Inicializar el arreglo que contendrá todas las asignaturas del alumno
                        $asignaturasTotales = [];
                        $especialidad = null;


                        $planAsignaturasGenerales = DB::table('plan_asignatura')
                            ->join('asignatura as asi', 'plan_asignatura.idAsignatura', '=', 'asi.id')
                            ->whereNull('plan_asignatura.idEspecilidad')
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
                            $alumno->especialidads()->attach($especialidad->id, [
                                'semestreInicio' => $grupoSemestre->semestre->numero
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

                        // Generar un salón aleatorio donde se impartirán las clases
                        $salon = 'Aula ' . rand(1, 20);

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
                            $idEspecialidad = $plan ? $plan->idEspecilidad : null;

                            // Crear el registro de clase con toda la información:
                            // - Salón donde se imparte
                            // - Qué asignatura es
                            // - En qué grupo-semestre
                            // - Si pertenece a una especialidad (NULL = tronco común)
                            $docente->clases()->firstOrCreate([
                                'idAsignatura' => $asigId,
                                'idGrupoSemestre' => $grupoSemestre->id,
                                'idEspecialidad' => $idEspecialidad,
                            ], ['salonClase' => $salon]);
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
