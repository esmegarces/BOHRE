<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Cuentum;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $cuenta = Cuentum::where('correo', $request->correo)->first();

        if (!$cuenta || !Hash::check($request->contrasena, $cuenta->contrasena)) {
            return response()->json([
                'message' => 'Correo o contraseña incorrectos.',
                'success' => false
            ], 401);
        }

        $token = JWTAuth::fromUser($cuenta);

        return $this->respondWithToken($token, $cuenta); // 👈 Pasa la cuenta
    }

    public function me()
    {
        $user = auth()->user()->load('persona');
        return response()->json($user);
    }

    protected function respondWithToken($token, $cuenta)
    {
        $cuenta->load('persona:id,idCuenta,nombre,apellidoPaterno,apellidoMaterno'); // Solo carga id e idCuenta de persona

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => [
                'id' => $cuenta->id,
                'idPersona' => $cuenta->persona?->id,
                'rol' => $cuenta->rol,
                'nombre' => $cuenta->persona?->nombre,
                'apellidoPaterno' => $cuenta->persona?->apellidoPaterno,
                'apellidoMaterno' => $cuenta->persona?->apellidoMaterno,
            ],
        ]);
    }

    public function testToken()
    {
        try {
            $user = auth('api')->user();
            return response()->json([
                'authenticated' => auth('api')->check(),
                'user' => $user,
                'token_parsed' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}
