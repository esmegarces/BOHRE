<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AlumnoGeneracion
 * 
 * @property int $id
 * @property int $semestreInicial
 * @property int $idAlumno
 * @property int $idGeneracion
 * 
 * @property Alumno $alumno
 * @property Generacion $generacion
 *
 * @package App\Models
 */
class AlumnoGeneracion extends Model
{
	protected $table = 'alumno_generacion';
	public $timestamps = false;

	protected $casts = [
		'semestreInicial' => 'int',
		'idAlumno' => 'int',
		'idGeneracion' => 'int'
	];

	protected $fillable = [
		'semestreInicial',
		'idAlumno',
		'idGeneracion'
	];

	public function alumno()
	{
		return $this->belongsTo(Alumno::class, 'idAlumno');
	}

	public function generacion()
	{
		return $this->belongsTo(Generacion::class, 'idGeneracion');
	}
}
