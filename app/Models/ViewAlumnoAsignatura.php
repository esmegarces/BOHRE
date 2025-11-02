<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ViewAlumnoAsignatura
 * 
 * @property int $idAlumno
 * @property int $idPersona
 * @property string $nombre
 * @property string $apellidoPaterno
 * @property string $apellidoMaterno
 * @property Carbon $fechaNacimiento
 * @property string $curp
 * @property string $nss
 * @property string $correo
 * @property string $telefono
 * @property string $rol
 * @property string $sexo
 * @property string $nia
 * @property string $situacion
 * @property string|null $cicloEscolar
 * @property string|null $generacion
 * @property string|null $especialidad
 * @property string|null $grupo
 * @property array|null $asignaturas
 *
 * @package App\Models
 */
class ViewAlumnoAsignatura extends Model
{
	protected $table = 'view_alumno_asignaturas';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'idAlumno' => 'int',
		'idPersona' => 'int',
		'fechaNacimiento' => 'datetime',
		'asignaturas' => 'json'
	];

	protected $fillable = [
		'idAlumno',
		'idPersona',
		'nombre',
		'apellidoPaterno',
		'apellidoMaterno',
		'fechaNacimiento',
		'curp',
		'nss',
		'correo',
		'telefono',
		'rol',
		'sexo',
		'nia',
		'situacion',
		'cicloEscolar',
		'generacion',
		'especialidad',
		'grupo',
		'asignaturas'
	];
}
