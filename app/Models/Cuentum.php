<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Cuentum extends Authenticatable implements JWTSubject
{
    use SoftDeletes, HasFactory;

    protected $table = 'cuenta';

    protected $fillable = [
        'correo',
        'contrasena',
        'rol'
    ];

    public function persona()
    {
        return $this->hasOne(Persona::class, 'idCuenta');
    }

    // Solo estos dos métodos para JWT
    public function getJWTIdentifier()
    {
        return $this->getKey(); // Devuelve el ID
    }

    public function getJWTCustomClaims()
    {
        $persona = $this->persona;

        return [
            'id' => $this->id,
            'idPersona' => $persona?->id,
            'rol' => $this->rol,
            'nombre' => $persona?->nombre,
        ];
    }
}
