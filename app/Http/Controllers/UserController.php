<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Alumno;
use App\Models\Cuentum;
use App\Models\Direccion;
use App\Models\Docente;
use App\Models\Persona;
use Illuminate\Http\Request;

class UserController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\UserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserRequest $request)
    {
        try {
            $usuario = \DB::transaction(function () use ($request) {
                $Direccion = Direccion::create([
                    'numeroCasa' => $request->numeroCasa,
                    'calle' => $request->calle,
                    'idLocalidad' => $request->idLocalidad
                ]);

                $cuenta = Cuentum::create([
                    'correo' => $request->correo,
                    'contrasena' => $request->contrasena,
                    'rol' => $request->rol,

                ]);
                $persona = Persona::create([
                    'nombre' => $request->nombre,
                    'apellidoPaterno' => $request->apellidoPaterno,
                    'apellidoMaterno' => $request->apellidoMaterno,
                    'curp' => $request->curp,
                    'telefono' => $request->telefono,
                    'sexo' => $request->sexo,
                    'fechaNacimiento' => $request->fechaNacimiento,
                    'nss' => $request->nss,
                    'idDireccion' => $Direccion->id,
                    'idCuenta' => $cuenta->id,
                ]);
                //Condicional para retornar los datos dependiendo su rol si es docente
                if ($cuenta->rol == 'docente') {
                    $Docente = Docente::create([
                        'cedulaProfesional' => $request->cedulaProfesional,
                        'numeroExpediente' => $request->numeroExpediente,
                        'idPersona' => $persona->id,
                    ]);
                    //Condicional en caso de que es un alumno aparecen sus datos correspondientes a su modelo
                } else if ($cuenta->rol == 'alumno') {
                    $Alumno = Alumno::create([
                        'nia' => $request->nia,
                        'numeroLista' => 0,
                        'situacion' => $request->situacion,
                        'idPersona' => $persona->id,
                    ]);

                }
                return $persona;
            });
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
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\UserRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
