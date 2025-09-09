<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Persona
 * 
 * @property int $id
 * @property string $nombre
 * @property string $apellidoPaterno
 * @property string $apellidoMaterno
 * @property string $curp
 * @property string $telefono
 * @property string $sexo
 * @property int $idDireccion
 * @property int $idCuenta
 * 
 * @property Direccion $direccion
 * @property Cuentum $cuentum
 * @property Collection|Alumno[] $alumnos
 * @property Collection|Docente[] $docentes
 *
 * @package App\Models
 */
class Persona extends Model
{
	protected $table = 'persona';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int',
		'idDireccion' => 'int',
		'idCuenta' => 'int'
	];

	protected $fillable = [
		'nombre',
		'apellidoPaterno',
		'apellidoMaterno',
		'curp',
		'telefono',
		'sexo',
		'idDireccion',
		'idCuenta'
	];

	public function direccion()
	{
		return $this->belongsTo(Direccion::class, 'idDireccion');
	}

	public function cuentum()
	{
		return $this->belongsTo(Cuentum::class, 'idCuenta');
	}

	public function alumnos()
	{
		return $this->hasMany(Alumno::class, 'idPersona');
	}

	public function docentes()
	{
		return $this->hasMany(Docente::class, 'idPersona');
	}
}
