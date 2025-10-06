<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Alumno;
use App\Models\Cuentum;
use App\Models\Direccion;
use App\Models\Docente;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    /**
     * Guardar un usuario
     *
     * @param \Illuminate\Http\UserRequest $request validacion de datos de usuario
     * @return \Illuminate\Http\JsonResponse respuesta JSON
     */
    public function store(UserRequest $request)
    {
        try {
            $usuario = \DB::transaction(function () use ($request) {
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
                    $docente = Docente::create([
                        'cedulaProfesional' => $request->cedulaProfesional,
                        'numeroExpediente' => $request->numeroExpediente,
                        'idPersona' => $persona->id,
                    ]);

                    //si no, en caso de alumno, se guarda en la tabla correspondiente
                } else if ($cuenta->rol == 'alumno') {
                    $alumno = Alumno::create([
                        'nia' => $request->nia,
                        //'numeroLista' => 0,
                        'situacion' => $request->situacion,
                        'idPersona' => $persona->id,
                    ]);

                }

                // obteniendo el registro con informacion general del usuario, sin importar el rol que tenga
                $persona = Persona::join('cuenta as c', 'persona.idCuenta', '=', 'c.id')
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

                return $persona;
            });

            // mensaje de exito
            return response()->json([
                'message' => 'Usuario creado con éxito',
                'data' => $usuario
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el usuario',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obteniendo usuarios por su rol
     * @param Request $request peticion, de la cual extraeremos el rol
     * @return \Illuminate\Http\JsonResponse respuesta Json
     */
    public function show(Request $request)
    {
        // extrayendo el parametro rol de la peticion
        $rol = $request->get('rol');

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
            return response()->json(['message' => 'Usuarios encontrados', 'data' => $personas], 200);
        }

    }

    /**
     * Obtener la informacion completa especifica de acuerdo al rol y id de un usuario
     * @param Request $request peticion
     * @return \Illuminate\Http\JsonResponse respuesta JSON
     */
    public function showByRol(Request $request)
    {
        // extrayendo el rol y id de la peticion
        $rol = $request->get('rol');
        $idPersona = $request->get('idPersona');

        // validando que vengan los datos necesarios en la peticion
        if (!$rol || !$idPersona) {
            return response()->json(['message' => 'El rol y el id de personas son requeridos', 'data' => null], 406);
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
            'persona.sexo',
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
            return response()->json(['message' => 'Usuario encontrado', 'data' => $persona], 200);
        }
    }

    /**
     * Actualizar la informacion de un usuario
     *
     * @param \Illuminate\Http\UserRequest $request validacion de datos de usuario
     * @param int $id id del usuario a actualizar (persona)
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserRequest $request, $id)
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
                $direccionData = $request->only(['numeroCasa', 'calle', 'idLocalidad']);
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
                    $cuentaData['rol'] = $request->rol;
                }

                if (!empty($cuentaData)) {
                    $cuenta->update($cuentaData);
                }

                // actualizar persona
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

                if (!empty($personaData)) {
                    $persona->update($personaData);
                }

                // actualizar datos específicos por rol
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
                    'persona.sexo',
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
                    case 'ALUMNO':
                        $query->join('alumno as a', 'persona.id', '=', 'a.idPersona');
                        $select[] = 'a.nia';
                        $select[] = 'a.situacion';
                        break;

                    case 'DOCENTE':
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
            ], 200);

        } catch (\Exception $e) {
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
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

            DB::transaction(function () use ($persona, $cuenta) {
                // eliminando registros relacionados en tablas dependientes
                if ($cuenta->rol === 'docente') {
                    Docente::where('idPersona', $persona->id)->delete();
                } elseif ($cuenta->rol === 'alumno') {
                    Alumno::where('idPersona', $persona->id)->delete();
                }

                // eliminando registros de persona, cuenta y direccion
                $persona->delete();
                $cuenta->delete();
            });

            return response()->json(['message' => 'Usuario eliminado con éxito', 'data' => null], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el usuario',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
