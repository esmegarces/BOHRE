<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
 * @property Carbon $fechaNacimiento
 * @property string $nss
 * @property int $idDireccion
 * @property int $idCuenta
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
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
	use SoftDeletes;
	protected $table = 'persona';
	public $incrementing = false;

	protected $casts = [
		'id' => 'int',
		'fechaNacimiento' => 'datetime',
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
		'fechaNacimiento',
		'nss',
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
