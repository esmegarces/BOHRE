<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Http\Requests\StoreAlumnoRequest;
use App\Http\Requests\UpdateAlumnoRequest;
use App\Models\CicloEscolar;
use App\Models\Generacion;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\isEmpty;

class AlumnoController extends Controller
{

    /**
     * Almacenar los datos extras de un alumno.
     *
     * @param StoreAlumnoRequest $request peticion http validada
     * @return JsonResponse respuesta en formato json
     */
    public function storeExtraData(StoreAlumnoRequest $request, int $id): JsonResponse
    {
        try {

            // busando si existe un alumno con el id proporcionado
            $alumno = Alumno::find($id);

            if (!$alumno) {
                return response()->json([
                    'error' => 'No se encontró un alumno con el ID proporcionado'
                ], 404);
            }

            $alumn = DB::transaction(callback: function () use ($request, $alumno) {
                // obteniendo el registro del alumno en grupo_semestre
                $alumnoGrupoSemestre = $alumno->grupo_semestres()->get();

                // si no existen registros o el grupo semestre nuevo no esta registrado
                if (!$alumnoGrupoSemestre || !$alumnoGrupoSemestre->contains('id', $request->idGrupoSemestre)) {
                    // guardamos la relacion del alumno con el gruposemestre
                    $alumno->grupo_semestres()->attach($request->idGrupoSemestre);
                }

                // recuperando el semestre al que pertenecera el alumno
                $semestre = $alumno->grupo_semestres()
                    ->where('grupo_semestre.id', $request->idGrupoSemestre)
                    ->first()
                    ?->semestre;

                // obtiendo la relacion del alumno con los ciclos escolares
                $alumnoCiclo = $alumno->alumno_ciclos()->get();

                $idCicloEscolar = $request->idCicloEscolar;

                if (!$idCicloEscolar) {
                    // Si no se proporciona ciclo escolar, buscar o crear el actual
                    $yearNow = Carbon::now()->year;
                    $cicloActual = CicloEscolar::where('anioInicio', '<=', $yearNow)
                        ->where('anioFin', '>=', $yearNow)
                        ->first();

                    if (!$cicloActual) {
                        $cicloActual = CicloEscolar::create([
                            'anioInicio' => $yearNow,
                            'anioFin' => $yearNow + 1
                        ]);
                    }
                    $idCicloEscolar = $cicloActual->id;
                }

                // Solo crear si no existe ya la relación
                if (!$alumnoCiclo->contains('idCicloEscolar', $idCicloEscolar)) {
                    $alumno->alumno_ciclos()->create([
                        'idCicloEscolar' => $idCicloEscolar,
                        'semestreCursado' => $semestre->numero
                    ]);
                }

                // obteniendo la relación del alumno con las generaciones
                $alumnoGeneracion = $alumno->generacions()->get();

                // Si el alumno ya tiene una generación asociada, no hacer nada
                if ($alumnoGeneracion->isEmpty()) {
                    if ($request->idGeneracion) {
                        // Buscar la generación por el id proporcionado
                        $generacion = Generacion::find($request->idGeneracion);

                        // Si no existe, crearla
                        if (!$generacion) {
                            $anioActual = Carbon::now()->year;
                            $fechaIngreso = Carbon::create($anioActual, 8, 1)->format('Y-m-d');
                            $fechaEgreso = Carbon::create($anioActual + 3, 8, 1)->format('Y-m-d');

                            $generacion = Generacion::create([
                                'anioIngreso' => $fechaIngreso,
                                'anioEgreso' => $fechaEgreso
                            ]);
                        }

                        // Asociar la generación al alumno
                        $alumno->generacions()->attach($generacion->id, [
                            'semestreInicial' => $semestre->numero
                        ]);
                    } else {
                        // Calcular la generación por el año de ingreso
                        $alumnoCreatedAt = $alumno->created_at->year;
                        $generacion = Generacion::whereYear('anioIngreso', '<=', $alumnoCreatedAt)
                            ->whereYear('anioEgreso', '>=', $alumnoCreatedAt)
                            ->first();

                        // Si no existe, crearla
                        if (!$generacion) {
                            $anioActual = Carbon::now()->year;
                            $fechaIngreso = Carbon::create($anioActual, 8, 1)->format('Y-m-d');
                            $fechaEgreso = Carbon::create($anioActual + 3, 8, 1)->format('Y-m-d');

                            $generacion = Generacion::create([
                                'anioIngreso' => $fechaIngreso,
                                'anioEgreso' => $fechaEgreso
                            ]);
                        }

                        // Asociar la generación al alumno
                        $alumno->generacions()->attach($generacion->id, [
                            'semestreInicial' => $semestre->numero
                        ]);
                    }
                }
                // Si ya tiene una generación, no hacer nada


                // arreglo para guardar las asignaturas que se le asignaran al alumno
                $asignaturas = [];

                // obtienendo las asignaturas comunes del semestre
                $asignaturas = $semestre->plan_asignaturas()
                    ->whereNull('idEspecilidad') // Solo las que no son de especialidad
                    ->with('asignatura')          // Incluye la info de la asignatura
                    ->get()
                    ->pluck('asignatura')         // Extrae directamente las asignaturas
                    ->toArray();

                // obteniendo las especialidades del alumno
                $especialidadesAlumno = $alumno->especialidads()->get();

                // si viene la especialidad
                if ($request->idEspecialidad && $especialidadesAlumno->isEmpty()) {
                    $alumno->especialidads()->attach($request->idEspecialidad, ['semestreInicio' => $semestre ? $semestre->numero : 0]);

                    // obtenemos las materias de la especialidad
                    $materiasEspecialidad = $alumno->especialidads()
                        ->where('especialidad.id', $request->idEspecialidad)
                        ->first()
                        ->plan_asignaturas()
                        ->where('idSemestre', $semestre->id)
                        ->with('asignatura')
                        ->get()
                        ->pluck('asignatura')
                        ->toArray();

                    // combinamos las asignaturas del semestre con las de la especialidad
                    $asignaturas = array_merge($asignaturas, $materiasEspecialidad);
                }

                // guardando en un archivo de texto las asignaturas que se le asignaran al alumno en public/pruebas y con la fecha actual
                $fileName = 'asignaturas_' . $alumno->id . '_' . date('Ymd_His') . '.txt';
                $filePath = public_path('pruebas/' . $fileName);
                file_put_contents($filePath, print_r($asignaturas, true));

                // TODO: ver que salones existen y como lo voy a enviar
                // asignando las asignaturas al alumno
                foreach ($asignaturas as $asignatura) {
                    $alumno
                        ->grupo_semestres()
                        ->where('grupo_semestre.id', $request->idGrupoSemestre)
                        ->first()
                        ->clases()->firstOrCreate([
                            'idAsignatura' => $asignatura['id'],
                            'idGrupoSemestre' => $request->idGrupoSemestre,
                            'idEspecialidad' => $asignatura['tipo'] == 'COMUN' ? null : ($request->idEspecialidad ?? null),
                        ], ['salonClase' => $asignatura['tipo'] == 'COMUN' ? 'COMUN' . $request->idGrupoSemestre : 'ESP' . $request->idGrupoSemestre]);
                }

                // TODO: RETURN ALUMNO WITH RELATIONS
                return $alumno;
            });

            return response()->json([
                'message' => 'Datos del alumno almacenados correctamente',
                'alumno' => $alumn
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error al almacenar los datos del alumno',
                'message' => $e->getMessage()],
                500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Alumno $alumno
     * @return \Illuminate\Http\Response
     */
    public function show(Alumno $alumno)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Alumno $alumno
     * @return \Illuminate\Http\Response
     */
    public function edit(Alumno $alumno)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateAlumnoRequest $request
     * @param \App\Models\Alumno $alumno
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAlumnoRequest $request, Alumno $alumno)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Alumno $alumno
     * @return \Illuminate\Http\Response
     */
    public function destroy(Alumno $alumno)
    {
        //
    }
}
