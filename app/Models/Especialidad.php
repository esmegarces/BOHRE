<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Especialidad
 *
 * @property int $id
 * @property string $nombre
 *
 * @property Collection|Alumno[] $alumnos
 * @property Collection|Clase[] $clases
 * @property Collection|PlanAsignatura[] $plan_asignaturas
 *
 * @package App\Models
 */
class Especialidad extends Model
{
	protected $table = 'especialidad';
	public $timestamps = false;

	protected $fillable = [
		'nombre'
	];

	public function alumnos()
	{
		return $this->belongsToMany(Alumno::class, 'alumno_especialidad', 'idEspecialidad', 'idAlumno')
					->withPivot('id', 'semestreInicio');
	}

	public function clases()
	{
		return $this->hasMany(Clase::class, 'idEspecialidad');
	}

	public function plan_asignaturas()
	{
		return $this->hasMany(PlanAsignatura::class, 'idEspecialidad');
	}
}
