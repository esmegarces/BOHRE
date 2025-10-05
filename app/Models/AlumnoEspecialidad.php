<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AlumnoEspecialidad
 * 
 * @property int $id
 * @property int $idAlumno
 * @property int $idEspecialidad
 * @property int $semestreInicio
 * 
 * @property Alumno $alumno
 * @property Especialidad $especialidad
 *
 * @package App\Models
 */
class AlumnoEspecialidad extends Model
{
	protected $table = 'alumno_especialidad';
	public $timestamps = false;

	protected $casts = [
		'idAlumno' => 'int',
		'idEspecialidad' => 'int',
		'semestreInicio' => 'int'
	];

	protected $fillable = [
		'idAlumno',
		'idEspecialidad',
		'semestreInicio'
	];

	public function alumno()
	{
		return $this->belongsTo(Alumno::class, 'idAlumno');
	}

	public function especialidad()
	{
		return $this->belongsTo(Especialidad::class, 'idEspecialidad');
	}
}
