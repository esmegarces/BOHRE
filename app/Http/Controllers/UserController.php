<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Alumno;
use App\Models\Clase;
use App\Models\Cuentum;
use App\Models\Direccion;
use App\Models\Docente;
use App\Models\Generacion;
use App\Models\Persona;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

class UserController extends Controller
{

    /**
     * @return JsonResponse respuesta JSON
     * @throws Throwable en caso de error en la transaccion
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $usuario = DB::transaction(function () use ($request) {
                // Crear dirección, cuenta y persona (igual que antes)
                $direccion = Direccion::create([
                    'numeroCasa' => $request->numeroCasa,
                    'calle' => strtoupper($request->calle),
                    'idLocalidad' => $request->idLocalidad
                ]);

                $cuenta = Cuentum::create([
                    'correo' => strtoupper($request->correo),
                    'contrasena' => Hash::make($request->contrasena),
                    'rol' => $request->rol,
                ]);

                $persona = Persona::create([
                    'nombre' => strtoupper($request->nombre),
                    'apellidoPaterno' => strtoupper($request->apellidoPaterno),
                    'apellidoMaterno' => strtoupper($request->apellidoMaterno),
                    'curp' => strtoupper($request->curp),
                    'telefono' => $request->telefono,
                    'sexo' => $request->sexo,
                    'fechaNacimiento' => $request->fechaNacimiento,
                    'nss' => $request->nss,
                    'idDireccion' => $direccion->id,
                    'idCuenta' => $cuenta->id,
                ]);

                if ($cuenta->rol == 'docente') {
                    Docente::create([
                        'cedulaProfesional' => $request->cedulaProfesional,
                        'numeroExpediente' => $request->numeroExpediente,
                        'idPersona' => $persona->id,
                    ]);

                } else if ($cuenta->rol == 'alumno') {
                    $alumno = Alumno::create([
                        'nia' => $request->nia,
                        'situacion' => 'ACTIVO',
                        'idPersona' => $persona->id,
                    ]);

                    // Asignar al grupo-semestre
                    $alumno->grupo_semestres()->attach($request->idGrupoSemestre);

                    // Recuperar semestre
                    $semestre = $alumno->grupo_semestres()
                        ->where('grupo_semestre.id', $request->idGrupoSemestre)
                        ->first()
                        ?->semestre;

                    // Asociar generación
                    $generacion = Generacion::find($request->idGeneracion);
                    $alumno->generacions()->attach($generacion->id, [
                        'semestreInicial' => $semestre->numero
                    ]);

                    // Si tiene especialidad, registrarla
                    if ($request->idEspecialidad) {
                        $alumno->especialidads()->attach($request->idEspecialidad, [
                            'semestreInicio' => $semestre->numero
                        ]);
                    }

                    // IMPORTANTE: Obtener las clases YA EXISTENTES del año actual
                    $anioActual = now()->year;

                    $clases = Clase::where('idGrupoSemestre', $request->idGrupoSemestre)
                        ->where('anio', $anioActual)
                        ->where(function ($query) use ($request) {
                            $query->whereNull('idEspecialidad') // Tronco común
                            ->orWhere('idEspecialidad', $request->idEspecialidad); // Su especialidad
                        })
                        ->get();

                    // Crear calificaciones para cada clase
                    foreach ($clases as $clase) {
                        $alumno->calificacions()->firstOrCreate([
                            'idAlumno' => $alumno->id,
                            'idClase' => $clase->id,
                        ], [
                            'momento1' => 0,
                            'momento2' => 0,
                            'momento3' => 0,
                        ]);
                    }
                }

                return Persona::join('cuenta as c', 'persona.idCuenta', '=', 'c.id')
                    ->select(
                        'persona.id',
                        'persona.nombre',
                        'persona.apellidoPaterno',
                        'persona.apellidoMaterno',
                        'persona.curp',
                        DB::raw("IF(persona.sexo = 'F', 'FEMENINO', 'MASCULINO') as sexo"),
                        'persona.nss',
                        'c.rol'
                    )
                    ->where('persona.id', $persona->id)
                    ->first();
            });

            return response()->json([
                'message' => 'Usuario creado con éxito',
                'data' => $usuario
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al crear el usuario',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obteniendo usuarios por su rol
     * @param Request $request peticion, de la cual extraeremos el rol
     * @return JsonResponse respuesta Json
     */
    public function show(Request $request)
    {
        // extrayendo el parametro rol de la peticion
        $rol = strtolower($request->get('rol'));

        // validando que el rol sea uno de los permitidos
        if ($rol && !in_array($rol, ['alumno', 'docente', 'admin'])) {
            return response()->json(['message' => 'El rol proporcionado no es válido', 'data' => null], 406);
        }

        // obteniendo los usuarios de acuerdo al rol y paginando
        $personas = Persona::join('cuenta as c', 'persona.idCuenta', '=', 'c.id')
            ->select(
                'persona.id',
                'persona.nombre',
                'persona.apellidoPaterno',
                'persona.apellidoMaterno',
                'persona.curp',
                DB::raw("IF(persona.sexo = 'F', 'FEMENINO', 'MASCULINO') as sexo"),
                'persona.nss',
                'c.rol'
            )
            ->when($rol, function ($query, $rol) {
                return $query->where('c.rol', $rol);
            })
            ->paginate(15);

        // evaluar el caso en que no existan registros en la db
        if ($personas->isEmpty()) {
            return response()->json(['message' => 'Usuarios no encontrados', 'data' => null], 404);
        } else {
            return response()->json(['message' => 'Usuarios recuperados con éxito', 'data' => $personas]);
        }

    }

    public function showDeletes(Request $request)
    {
        // extrayendo el parametro rol de la peticion
        $rol = strtolower($request->get('rol'));

        // validando que el rol sea uno de los permitidos
        if ($rol && !in_array($rol, ['alumno', 'docente', 'admin'])) {
            return response()->json(['message' => 'El rol proporcionado no es válido', 'data' => null], 406);
        }

        // obteniendo los usuarios eliminados de acuerdo al rol
        $personas = Persona::withTrashed()
            ->join('cuenta as c', function ($join) {
                $join->on('persona.idCuenta', '=', 'c.id');
            })
            ->select(
                'persona.id',
                'persona.nombre',
                'persona.apellidoPaterno',
                'persona.apellidoMaterno',
                'persona.curp',
                DB::raw("IF(persona.sexo = 'F', 'FEMENINO', 'MASCULINO') as sexo"),
                'persona.nss',
                'c.rol'
            )
            ->where(function ($query) {
                $query->whereNotNull('persona.deleted_at')
                    ->orWhereNotNull('c.deleted_at');
            })
            ->when($rol, function ($query, $rol) {
                return $query->where('c.rol', $rol);
            })
            ->paginate(15);

        // evaluar el caso en que no existan registros en la db
        if ($personas->isEmpty()) {
            return response()->json(['message' => 'No hay usuarios eliminados', 'data' => null], 404);
        } else {
            return response()->json(['message' => 'Usuarios recuperados con éxito', 'data' => $personas]);
        }

    }

    /**
     * Obtener la informacion completa especifica de acuerdo al rol y id de un usuario
     * @return JsonResponse respuesta JSON
     */
    public function retrieveByRol(string $rol, int $id): JsonResponse
    {
        // extrayendo el rol y id de la peticion
        $rol = strtolower($rol);
        $idPersona = $id;

        // validando que vengan los datos necesarios en la peticion
        if (!$rol || !$idPersona) {
            return response()->json(['message' => 'El rol y el id de personas son requeridos', 'data' => null], 406);
        }

        // validando que el rol sea uno de los permitidos
        if (!in_array($rol, ['alumno', 'docente', 'admin'])) {
            return response()->json(['message' => 'El rol proporcionado no es válido', 'data' => null], 406);
        }

        // query para obtener la data completa del usuario correspondiente de acuerdo al rol y id
        // Base query (datos comunes a todos los roles)
        $query = DB::table('persona')
            ->join('cuenta as c', 'persona.idCuenta', '=', 'c.id')
            ->join('direccion as d', 'persona.idDireccion', '=', 'd.id')
            ->join('localidad as l', 'd.idLocalidad', '=', 'l.id')
            ->join('municipio as m', 'l.idMunicipio', '=', 'm.id')
            ->where('c.rol', $rol)
            ->where('persona.id', $idPersona);

        // Select base
        $select = [
            'persona.id',
            'persona.nombre',
            'persona.apellidoPaterno',
            'persona.apellidoMaterno',
            'persona.curp',
            'persona.telefono',
            DB::raw("IF(persona.sexo = 'F', 'FEMENINO', 'MASCULINO') as sexo"),
            'persona.fechaNacimiento',
            'persona.nss',
            'c.correo',
            'c.rol',
            'm.id as idMunicipio',
            'm.nombre as municipio',
            'l.id as idLocalidad',
            'l.nombre as localidad',
            'l.codigoPostal',
            'd.numeroCasa',
            'd.calle',
        ];

        // Agregar joins y columnas específicas según el rol
        switch ($rol) {
            case 'alumno':
                $query->join('alumno as a', 'persona.id', '=', 'a.idPersona');
                $query->join('alumno_grupo_semestre as ags', 'a.id', '=', 'ags.idAlumno');
                $query->join('grupo_semestre as gs', 'ags.idGrupoSemestre', '=', 'gs.id');
                $query->join('semestre as s', 'gs.idSemestre', '=', 's.id');
                $query->join('grupo as g', 'g.id', '=', 'gs.idGrupo');
                $query->join('alumno_generacion as ag', 'ag.idAlumno', '=', 'a.id');
                $query->join('generacion as gen', 'gen.id', '=', 'ag.idGeneracion');
                $query->leftJoin('alumno_especialidad as aesp', 'aesp.idAlumno', '=', 'a.id');
                $query->leftJoin('especialidad as esp', 'esp.id', '=', 'aesp.idEspecialidad');

                // Filtrar para traer solo el semestre con el número máximo (más grande) del alumno
                // Filtrar para traer solo el semestre con el número máximo (más grande) del alumno
                $query->whereRaw(
                    's.numero = (
                SELECT MAX(se.numero)
                FROM semestre se
                JOIN grupo_semestre gs2 ON se.id = gs2.idSemestre
                JOIN alumno_grupo_semestre ags2 ON gs2.id = ags2.idGrupoSemestre
                JOIN alumno a2 ON ags2.idAlumno = a2.id
                WHERE a2.idPersona = ?
            )',
                    [$idPersona]
                );

                $select[] = 'a.nia';
                $select[] = 'a.situacion';
                $select[] = 'gs.id as idGrupoSemestre';
                $select[] = 's.numero as numeroSemestre';
                //$select[] = 's.periodo as periodoSemestre';
                $select[] = 'gen.id as idGeneracion';
                $select[] = 'gen.fechaIngreso as fechaIngresoGeneracion';
                $select[] = 'gen.fechaEgreso as fechaEgresoGeneracion';
                $select[] = 'esp.id as idEspecialidad';
                $select[] = 'esp.nombre as especialidadNombre';
                break;

            case 'docente':
                $query->join('docente as dc', 'persona.id', '=', 'dc.idPersona');
                $select[] = 'dc.cedulaProfesional';
                $select[] = 'dc.numeroExpediente';
                break;
        }

        $persona = $query->select($select)->first();

        // evaluando si no se encuentra
        if (!$persona) {
            return response()->json(['message' => 'Usuario no encontrado', 'data' => null], 404);

        } else {
            return response()->json(['message' => 'Usuario encontrado', 'data' => $persona]);
        }
    }

    /**
     * Actualizar la informacion de un usuario
     *
     * @param UpdateUserRequest $request validacion de datos de usuario
     * @param int $id del usuario a actualizar (persona)
     * @return JsonResponse
     * @throws Throwable en caso de error en la transaccion
     */
    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        $persona = Persona::find($id);

        if (!$persona) {
            return response()->json(['message' => 'Usuario no encontrado', 'data' => null], 404);
        }

        try {
            $person = DB::transaction(function () use ($request, $persona) {

                // ============================================
                // 1. ACTUALIZAR DIRECCIÓN
                // ============================================
                $direccion = Direccion::find($persona->idDireccion);
                $direccionData = $request->only(['numeroCasa', 'calle', 'idLocalidad']);

                if (isset($direccionData['calle'])) {
                    $direccionData['calle'] = strtoupper($direccionData['calle']);
                }

                if (!empty($direccionData)) {
                    $direccion->update($direccionData);
                }

                // ============================================
                // 2. ACTUALIZAR CUENTA
                // ============================================
                $cuenta = $persona->cuentum;
                $cuentaData = [];

                if ($request->has('correo')) {
                    $cuentaData['correo'] = strtoupper($request->correo);
                }
                if ($request->has('contrasena')) {
                    $cuentaData['contrasena'] = Hash::make($request->contrasena);
                }

                if (!empty($cuentaData)) {
                    $cuenta->update($cuentaData);
                }

                // ============================================
                // 3. ACTUALIZAR PERSONA
                // ============================================
                $personaData = $request->only([
                    'nombre', 'apellidoPaterno', 'apellidoMaterno',
                    'curp', 'telefono', 'sexo', 'fechaNacimiento', 'nss'
                ]);

                $upperKeys = ['nombre', 'apellidoPaterno', 'apellidoMaterno', 'curp'];
                foreach ($upperKeys as $key) {
                    if (isset($personaData[$key])) {
                        $personaData[$key] = strtoupper($personaData[$key]);
                    }
                }

                if (!empty($personaData)) {
                    $persona->update($personaData);
                }

                $rolCuenta = strtolower($cuenta->rol);

                // ============================================
                // 4. ACTUALIZAR POR ROL
                // ============================================
                if ($rolCuenta === 'docente') {
                    $this->actualizarDocente($request, $persona);

                } elseif ($rolCuenta === 'alumno') {
                    $this->actualizarAlumno($request, $persona);
                }

                // ============================================
                // 5. RETORNAR DATOS ACTUALIZADOS
                // ============================================
                return $this->obtenerDatosUsuario($persona->id, $rolCuenta);
            });

            return response()->json([
                'message' => 'Usuario actualizado con éxito',
                'data' => $person
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el usuario',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

// ============================================
// MÉTODOS AUXILIARES
// ============================================

    private function actualizarDocente($request, $persona)
    {
        $docente = Docente::where('idPersona', $persona->id)->first();

        if (!$docente) {
            return;
        }

        $docenteData = $request->only(['cedulaProfesional', 'numeroExpediente']);

        if (!empty($docenteData)) {
            $docente->update($docenteData);
        }
    }

    private function actualizarAlumno($request, $persona)
    {
        $alumno = Alumno::where('idPersona', $persona->id)->first();

        if (!$alumno) {
            return;
        }

        // Actualizar datos básicos del alumno
        $alumnoData = $request->only(['nia']);
        if (!empty($alumnoData)) {
            $alumno->update($alumnoData);
        }

        // Si cambia de grupo-semestre
        if ($request->has('idGrupoSemestre')) {
            $this->cambiarGrupoSemestre($alumno, $request);
        }

        // Si cambia de especialidad (solo si está en 3er semestre o superior)
        if ($request->has('idEspecialidad')) {
            $this->cambiarEspecialidad($alumno, $request);
        }
    }

    private function cambiarGrupoSemestre($alumno, $request)
    {
        $grupoSemestreAnterior = $alumno->grupo_semestres()->first();
        $nuevoGrupoSemestreId = $request->idGrupoSemestre;

        // Si no cambió, no hacer nada
        if ($grupoSemestreAnterior && $grupoSemestreAnterior->id == $nuevoGrupoSemestreId) {
            return;
        }

        // Actualizar relación grupo-semestre
        $alumno->grupo_semestres()->sync([$nuevoGrupoSemestreId]);

        // Obtener el nuevo semestre
        $nuevoSemestre = $alumno->grupo_semestres()->first()?->semestre;

        if (!$nuevoSemestre) {
            throw new Exception('No se pudo obtener el semestre del nuevo grupo');
        }

        // Obtener especialidad actual del alumno
        $especialidadAlumno = $alumno->especialidads()->first();
        $anioActual = now()->year;

        // Obtener las clases YA EXISTENTES para este grupo-semestre
        $clasesDisponibles = Clase::where('idGrupoSemestre', $nuevoGrupoSemestreId)
            ->where('anio', $anioActual)
            ->where(function ($query) use ($especialidadAlumno) {
                $query->whereNull('idEspecialidad'); // Tronco común

                if ($especialidadAlumno) {
                    $query->orWhere('idEspecialidad', $especialidadAlumno->id);
                }
            })
            ->get();

        if ($clasesDisponibles->isEmpty()) {
            throw new Exception("No existen clases creadas para este grupo-semestre en el año {$anioActual}. Ejecute: php artisan clases:generar {$anioActual}");
        }

        // Crear calificaciones para las nuevas clases (solo si no existen)
        foreach ($clasesDisponibles as $clase) {
            $alumno->calificacions()->firstOrCreate([
                'idAlumno' => $alumno->id,
                'idClase' => $clase->id, // IMPORTANTE: usa idClase
            ], [
                'momento1' => 0,
                'momento2' => 0,
                'momento3' => 0,
            ]);
        }
    }

    private function cambiarEspecialidad($alumno, $request)
    {
        $semestreActual = $alumno->grupo_semestres()->first()?->semestre;

        // Solo se puede tener especialidad desde 3er semestre
        if (!$semestreActual || $semestreActual->numero < 3) {
            return;
        }

        $especialidadAnterior = $alumno->especialidads()->first();
        $nuevaEspecialidadId = $request->idEspecialidad;

        // Si no cambió, no hacer nada
        if ($especialidadAnterior && $especialidadAnterior->id == $nuevaEspecialidadId) {
            return;
        }

        // Actualizar especialidad
        $alumno->especialidads()->sync([
            $nuevaEspecialidadId => ['semestreInicio' => $semestreActual->numero]
        ]);

        // Obtener grupo-semestre actual
        $grupoSemestreId = $alumno->grupo_semestres()->first()->id;
        $anioActual = now()->year;

        // Obtener clases de la nueva especialidad
        $clasesEspecialidad = Clase::where('idGrupoSemestre', $grupoSemestreId)
            ->where('anio', $anioActual)
            ->where('idEspecialidad', $nuevaEspecialidadId)
            ->get();

        if ($clasesEspecialidad->isEmpty()) {
            throw new Exception("No existen clases de esta especialidad para este grupo-semestre en el año {$anioActual}");
        }

        // Crear calificaciones para las clases de especialidad
        foreach ($clasesEspecialidad as $clase) {
            $alumno->calificacions()->firstOrCreate([
                'idAlumno' => $alumno->id,
                'idClase' => $clase->id,
            ], [
                'momento1' => 0,
                'momento2' => 0,
                'momento3' => 0,
            ]);
        }

        // OPCIONAL: Eliminar calificaciones de la especialidad anterior si existía
        if ($especialidadAnterior) {
            $clasesAnteriores = Clase::where('idGrupoSemestre', $grupoSemestreId)
                ->where('anio', $anioActual)
                ->where('idEspecialidad', $especialidadAnterior->id)
                ->pluck('id');

            // Eliminar calificaciones de la especialidad anterior
            $alumno->calificacions()
                ->whereIn('idClase', $clasesAnteriores)
                ->delete();
        }
    }

    private function obtenerDatosUsuario($personaId, $rol)
    {
        $query = DB::table('persona')
            ->join('cuenta as c', 'persona.idCuenta', '=', 'c.id')
            ->join('direccion as d', 'persona.idDireccion', '=', 'd.id')
            ->join('localidad as l', 'd.idLocalidad', '=', 'l.id')
            ->join('municipio as m', 'l.idMunicipio', '=', 'm.id')
            ->where('persona.id', $personaId)
            ->where('c.rol', $rol);

        $select = [
            'persona.id',
            'persona.nombre',
            'persona.apellidoPaterno',
            'persona.apellidoMaterno',
            'persona.curp',
            'persona.telefono',
            DB::raw("IF(persona.sexo = 'F', 'FEMENINO', 'MASCULINO') as sexo"),
            'persona.fechaNacimiento',
            'persona.nss',
            'c.correo',
            'c.rol',
            'm.id as idMunicipio',
            'm.nombre as municipio',
            'l.id as idLocalidad',
            'l.nombre as localidad',
            'l.codigoPostal',
            'd.numeroCasa',
            'd.calle',
        ];

        switch ($rol) {
            case 'alumno':
                $query->join('alumno as a', 'persona.id', '=', 'a.idPersona')
                    ->join('alumno_grupo_semestre as ags', 'a.id', '=', 'ags.idAlumno')
                    ->join('grupo_semestre as gs', 'ags.idGrupoSemestre', '=', 'gs.id')
                    ->join('semestre as s', 'gs.idSemestre', '=', 's.id')
                    ->join('grupo as g', 'g.id', '=', 'gs.idGrupo')
                    ->join('alumno_generacion as ag', 'ag.idAlumno', '=', 'a.id')
                    ->join('generacion as gen', 'gen.id', '=', 'ag.idGeneracion')
                    ->leftJoin('alumno_especialidad as aesp', 'aesp.idAlumno', '=', 'a.id')
                    ->leftJoin('especialidad as esp', 'esp.id', '=', 'aesp.idEspecialidad');

                $query->whereRaw('s.numero = (
                SELECT MAX(se.numero)
                FROM semestre se
                JOIN grupo_semestre gs2 ON se.id = gs2.idSemestre
                JOIN alumno_grupo_semestre ags2 ON gs2.id = ags2.idGrupoSemestre
                JOIN alumno a2 ON ags2.idAlumno = a2.id
                WHERE a2.idPersona = ?
            )', [$personaId]);

                $select = array_merge($select, [
                    'a.nia',
                    'a.situacion',
                    'gs.id as idGrupoSemestre',
                    's.numero as numeroSemestre',
                    'gen.id as idGeneracion',
                    'gen.fechaIngreso as fechaIngresoGeneracion',
                    'gen.fechaEgreso as fechaEgresoGeneracion',
                    'esp.id as idEspecialidad',
                    'esp.nombre as especialidadNombre',
                ]);
                break;

            case 'docente':
                $query->join('docente as dc', 'persona.id', '=', 'dc.idPersona');
                $select = array_merge($select, [
                    'dc.cedulaProfesional',
                    'dc.numeroExpediente',
                ]);
                break;
        }

        return $query->select($select)->first();
    }

    /**
     * Eliminar un usuario
     *
     * @param int $id de usuario a eliminar (persona)
     * @return JsonResponse
     * @throws Throwable en caso de error en la transaccion
     */
    public function destroy(int $id)
    {

        // buscando el usuario por su id (persona)
        $persona = Persona::find($id);

        // evaluando si no se encuentra el usuario en la db
        if (!$persona) {
            return response()->json(['message' => 'Usuario no encontrado', 'data' => null], 404);
        }

        try {
            // obteniendo la cuenta del usuario
            $cuenta = Cuentum::find($persona->idCuenta);

            // transaccion para eliminar registros en tablas dependientes y la persona, cuenta y direccion (tod_o o nada)
            DB::transaction(function () use ($persona, $cuenta) {
                // eliminando registros relacionados en tablas dependientes
                if ($cuenta->rol === 'docente') {
                    Docente::where('idPersona', $persona->id)->delete();
                } elseif ($cuenta->rol === 'alumno') {
                    Alumno::where('idPersona', $persona->id)->delete();
                }

                // eliminando registros de persona y cuenta (como tiene soft delete, no se elimina de forma permanente, por lo que no es necesario eliminar la direccion)
                $persona->delete();
                $cuenta->delete();
            });

            return response()->json(['message' => 'Usuario eliminado con éxito', 'data' => null]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el usuario',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Eliminar un usuario definitivamente (soft delete)
     *
     * @param int $id de usuario a eliminar (persona)
     * @return JsonResponse
     * @throws Throwable en caso de error en la transaccion
     */
    public function destroyPermanently(int $id)
    {
        // Buscar la persona incluyendo las eliminadas con soft delete
        $persona = Persona::withTrashed()->find($id);

        if (!$persona) {
            return response()->json(['message' => 'Usuario no encontrado', 'data' => null], 404);
        }

        try {
            // Obtener la cuenta del usuario (incluyendo eliminadas)
            $cuenta = Cuentum::withTrashed()->find($persona->idCuenta);

            if (!$cuenta) {
                return response()->json(['message' => 'Cuenta no encontrada', 'data' => null], 404);
            }

            // Obtener el ID de la dirección antes de eliminar
            $idDireccion = $persona->idDireccion;

            // Transacción para eliminar registros
            DB::transaction(function () use ($persona, $cuenta, $idDireccion) {

                // 1. Eliminar dependencias que NO tienen CASCADE en sus foreign keys
                if ($cuenta->rol === 'alumno') {
                    // Obtener IDs de alumnos (incluyendo soft deleted si alumno usa SoftDeletes)
                    $alumnoIds = DB::table('alumno')
                        ->where('idPersona', $persona->id)
                        ->pluck('id')
                        ->toArray();

                    if (!empty($alumnoIds)) {
                        // Eliminar tablas pivot y relacionadas que NO tienen CASCADE
                        DB::table('alumno_especialidad')->whereIn('idAlumno', $alumnoIds)->delete();
                        DB::table('alumno_generacion')->whereIn('idAlumno', $alumnoIds)->delete();
                        DB::table('alumno_grupo_semestre')->whereIn('idAlumno', $alumnoIds)->delete();

                        // Otras tablas relacionadas
                        DB::table('calificacion')->whereIn('idAlumno', $alumnoIds)->delete();
                    }

                } elseif ($cuenta->rol === 'docente') {
                    // Obtener IDs de docentes
                    $docenteIds = DB::table('docente')
                        ->where('idPersona', $persona->id)
                        ->pluck('id')
                        ->toArray();

                    if (!empty($docenteIds)) {
                        // Eliminar clases asociadas
                        DB::table('clase')->whereIn('idDocente', $docenteIds)->delete();
                    }
                }

                // 2. Eliminar persona (CASCADE eliminará automáticamente alumno/docente)
                // Usando DB::table para hacer DELETE real, no soft delete
                DB::table('persona')->where('id', $persona->id)->delete();

                // 3. Eliminar cuenta
                DB::table('cuenta')->where('id', $cuenta->id)->delete();

                // 4. Eliminar dirección
                DB::table('direccion')->where('id', $idDireccion)->delete();
            });

            return response()->json([
                'message' => 'Usuario eliminado definitivamente con éxito',
                'data' => null
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el usuario',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Restaurar un usuario eliminado (soft delete)
     * @param int $id de usuario a restaurar (persona)
     * @return JsonResponse respuesta JSON
     * @throws Throwable en caso de error en la transaccion
     */
    public function restore(int $id)
    {
        try {
            // Buscar la persona eliminada (solo soft deleted)
            $persona = Persona::onlyTrashed()->find($id);

            if (!$persona) {
                return response()->json([
                    'message' => 'Usuario eliminado no encontrado',
                    'data' => null
                ], 404);
            }

            // Obtener la cuenta eliminada
            $cuenta = Cuentum::onlyTrashed()->find($persona->idCuenta);

            if (!$cuenta) {
                return response()->json([
                    'message' => 'Cuenta eliminada no encontrada',
                    'data' => null
                ], 404);
            }

            // Transacción para restaurar
            DB::transaction(function () use ($persona, $cuenta) {

                // Restaurar la persona
                $persona->restore();

                // Restaurar la cuenta
                $cuenta->restore();
            });

            // Obtener el usuario restaurado
            $usuario = Persona::join('cuenta as c', 'persona.idCuenta', '=', 'c.id')
                ->select(
                    'persona.id',
                    'persona.nombre',
                    'persona.apellidoPaterno',
                    'persona.apellidoMaterno',
                    'persona.curp',
                    DB::raw("IF(persona.sexo = 'F', 'FEMENINO', 'MASCULINO') as sexo"),
                    'persona.nss',
                    'c.rol'
                )
                ->where('persona.id', $id)
                ->first();

            return response()->json([
                'message' => 'Usuario restaurado con éxito',
                'data' => $usuario
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al restaurar el usuario',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getDocentes()
    {
        $docentes = DB::table('docente as d')
            ->join('persona as p', 'd.idPersona', '=', 'p.id')
            ->join('cuenta as c', 'p.idCuenta', '=', 'c.id')
            ->select(
                'd.id',
                DB::raw("CONCAT(p.nombre, ' ', p.apellidoPaterno, ' ', p.apellidoMaterno) as nombreCompleto"),
                'p.nombre',
                'p.apellidoPaterno',
                'p.apellidoMaterno',
                'd.cedulaProfesional',
                'c.correo'
            )
            ->whereNull('c.deleted_at')
            ->orderBy('p.apellidoPaterno')
            ->get();

        return response()->json([
            'message' => 'Docentes recuperados con éxito',
            'data' => $docentes
        ]);

    }
}
