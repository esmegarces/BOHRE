<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Alumno;
use App\Models\Cuentum;
use App\Models\Direccion;
use App\Models\Docente;
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
     * @param UserRequest $request
     * @return JsonResponse respuesta JSON
     * @throws Throwable en caso de error en la transaccion
     */
    public function store(UserRequest $request)
    {
        try {
            $usuario = DB::transaction(function () use ($request) {
                // almacenando el registro de direccion perteneciente al usuario
                $direccion = Direccion::create([
                    'numeroCasa' => $request->numeroCasa,
                    'calle' => $request->calle,
                    'idLocalidad' => $request->idLocalidad
                ]);

                // almacenando el registro de cuenta para el usuario
                $cuenta = Cuentum::create([
                    'correo' => $request->correo,
                    'contrasena' => Hash::make($request->contrasena),
                    'rol' => $request->rol,

                ]);

                // almacenando el registro de persona para el usuario
                $persona = Persona::create([
                    'nombre' => $request->nombre,
                    'apellidoPaterno' => $request->apellidoPaterno,
                    'apellidoMaterno' => $request->apellidoMaterno,
                    'curp' => $request->curp,
                    'telefono' => $request->telefono,
                    'sexo' => $request->sexo,
                    'fechaNacimiento' => $request->fechaNacimiento,
                    'nss' => $request->nss,
                    'idDireccion' => $direccion->id,
                    'idCuenta' => $cuenta->id,
                ]);


                // evaluando el rol del usuario para determinar en que tabla se debera guardar su informacion
                if ($cuenta->rol == 'docente') {
                    // si el rol es docente, se guarda en su respectiva tabla
                    Docente::create([
                        'cedulaProfesional' => $request->cedulaProfesional,
                        'numeroExpediente' => $request->numeroExpediente,
                        'idPersona' => $persona->id,
                    ]);

                    //si no, en caso de alumno, se guarda en la tabla correspondiente
                } else if ($cuenta->rol == 'alumno') {
                    Alumno::create([
                        'nia' => $request->nia,
                        'situacion' => $request->situacion,
                        'idPersona' => $persona->id,
                    ]);
                }

                // obteniendo el registro con informacion general del usuario, sin importar el rol que tenga
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
                    ->orderByDesc('persona.id')->first();
            });

            // mensaje de exito
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

    /**
     * Obtener la informacion completa especifica de acuerdo al rol y id de un usuario
     * @param Request $request peticion
     * @return JsonResponse respuesta JSON
     */
    public function showByRol(Request $request): JsonResponse
    {
        // extrayendo el rol y id de la peticion
        $rol = strtolower($request->get('rol'));
        $idPersona = $request->get('idPersona');

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
            'm.nombre as municipio',
            'l.nombre as localidad',
            'l.codigoPostal',
            'd.numeroCasa',
            'd.calle',
        ];

        // Agregar joins y columnas específicas según el rol
        switch ($rol) {
            case 'alumno':
                $query->join('alumno as a', 'persona.id', '=', 'a.idPersona');
                $select[] = 'a.nia';
                $select[] = 'a.situacion';
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
     * @param UserRequest $request validacion de datos de usuario
     * @param int $id del usuario a actualizar (persona)
     * @return JsonResponse
     * @throws Throwable en caso de error en la transaccion
     */
    public function update(UserRequest $request, int $id): JsonResponse
    {
        // buscando el usuario por su id (persona)
        $persona = Persona::find($id);

        // evaluando si no se encuentra el usuario en la db
        if (!$persona) {
            return response()->json(['message' => 'Usuario no encontrado', 'data' => null], 404);
        }

        try {
            $persona = DB::transaction(function () use ($request, $persona) {

                // actualizar dirección
                $direccion = Direccion::find($persona->idDireccion);

                // obtiene en un array solo los campos que pasaron la validacion de la request
                $direccionData = $request->only(['numeroCasa', 'calle', 'idLocalidad']);

                // si no esta vacio el array, actualiza
                if (!empty($direccionData)) {
                    $direccion->update($direccionData);
                }

                // actualizar cuenta
                $cuenta = Cuentum::find($persona->idCuenta);
                $cuentaData = [];

                if ($request->has('correo')) {
                    $cuentaData['correo'] = $request->correo;
                }
                if ($request->has('contrasena')) {
                    $cuentaData['contrasena'] = Hash::make($request->contrasena);
                }

                if ($request->has('rol')) {
                    // obteniendo los roles en minusculas para evitar problemas de comparacion
                    $rolCuenta = strtolower($cuenta->rol);
                    $rolRecibido = strtolower($request->rol);

                    // SI EL ROL ACTUAL ES DISTINTO AL RECIBIDO, eliminar registros antiguos y crear nuevos según el nuevo rol
                    if ($rolCuenta !== $rolRecibido) {

                        // agregando el nuevo rol al array de actualizacion
                        $cuentaData['rol'] = $rolRecibido;


                        // evalua el rol anterior para eliminar su registro correspondiente,
                        // forzando la eliminacion para evitar problemas de integridad referencial
                        switch ($rolCuenta) {
                            case 'docente':
                                // elimina el docente si el rol anterior era docente
                                Docente::where('idPersona', $persona->id)->forceDelete();
                                break;
                            case 'alumno':
                                // elimina el alumno si el rol anterior era alumno
                                Alumno::where('idPersona', $persona->id)->forceDelete();
                                break;
                        }

                        // evalua el nuevo rol para crear su registro correspondiente
                        switch ($rolRecibido) {
                            case 'docente':
                                Docente::create([
                                    'cedulaProfesional' => $request->cedulaProfesional,
                                    'numeroExpediente' => $request->numeroExpediente,
                                    'idPersona' => $persona->id,
                                ]);
                                break;
                            case 'alumno':
                                Alumno::create([
                                    'nia' => $request->nia,
                                    'situacion' => $request->situacion,
                                    'idPersona' => $persona->id,
                                ]);
                                break;
                        }
                        // el admin no fue necesario evaluarlo porque no existe una tabla dependiente para el rol admin
                    }
                }

                // actualiza la cuenta si el array no esta vacio
                if (!empty($cuentaData)) {
                    $cuenta->update($cuentaData);
                }

                // actualizar persona, de la request, en un array solo obtiene los campos que pasaron la validacion
                $personaData = $request->only([
                    'nombre',
                    'apellidoPaterno',
                    'apellidoMaterno',
                    'curp',
                    'telefono',
                    'sexo',
                    'fechaNacimiento',
                    'nss'
                ]);

                // si el array no esta vacio, actualiza
                if (!empty($personaData)) {
                    $persona->update($personaData);
                }

                // actualizar datos específicos por rol (en caso de que no haya cambiado el rol)
                if ($cuenta->rol === 'docente') {
                    $docente = Docente::where('idPersona', $persona->id)->first();
                    if ($docente) {
                        $docenteData = $request->only(['cedulaProfesional', 'numeroExpediente']);
                        if (!empty($docenteData)) {
                            $docente->update($docenteData);
                        }
                    }
                } elseif ($cuenta->rol === 'alumno') {
                    $alumno = Alumno::where('idPersona', $persona->id)->first();
                    if ($alumno) {
                        $alumnoData = $request->only(['nia', 'situacion']);
                        if (!empty($alumnoData)) {
                            $alumno->update($alumnoData);
                        }
                    }
                }

                // volver a consultar al usuario con la estructura correcta
                $query = DB::table('persona')
                    ->join('cuenta as c', 'persona.idCuenta', '=', 'c.id')
                    ->join('direccion as d', 'persona.idDireccion', '=', 'd.id')
                    ->join('localidad as l', 'd.idLocalidad', '=', 'l.id')
                    ->join('municipio as m', 'l.idMunicipio', '=', 'm.id')
                    ->where('persona.id', $persona->id)
                    ->where('c.rol', $cuenta->rol);

                // Campos base comunes a todos los roles
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
                    'm.nombre as municipio',
                    'l.nombre as localidad',
                    'l.codigoPostal',
                    'd.numeroCasa',
                    'd.calle',
                ];

                // Campos específicos según el rol
                switch ($cuenta->rol) {
                    case 'alumno':
                        $query->join('alumno as a', 'persona.id', '=', 'a.idPersona');
                        $select[] = 'a.nia';
                        $select[] = 'a.situacion';
                        break;

                    case 'docente':
                        $query->join('docente as dc', 'persona.id', '=', 'dc.idPersona');
                        $select[] = 'dc.cedulaProfesional';
                        $select[] = 'dc.numeroExpediente';
                        break;
                }

                return $query->select($select)->first();
            });

            return response()->json([
                'message' => 'Usuario actualizado con éxito',
                'data' => $persona
            ]);

        } catch
        (Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el usuario',
                'error' => $e->getMessage(),
            ], 500);
        }
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
}
