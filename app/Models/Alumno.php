<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Alumno
 *
 * @property int $id
 * @property string $nia
 * @property string $situacion
 * @property int $idPersona
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Persona $persona
 * @property Collection|AlumnoCiclo[] $alumno_ciclos
 * @property Collection|Especialidad[] $especialidads
 * @property Collection|Generacion[] $generacions
 * @property Collection|GrupoSemestre[] $grupo_semestres
 * @property Collection|Calificacion[] $calificacions
 *
 * @package App\Models
 */
class Alumno extends Model
{
	protected $table = 'alumno';

	protected $casts = [
		'idPersona' => 'int'
	];

	protected $fillable = [
		'nia',
		'situacion',
		'idPersona'
	];

	public function persona()
	{
		return $this->belongsTo(Persona::class, 'idPersona');
	}


	public function especialidads()
	{
		return $this->belongsToMany(Especialidad::class, 'alumno_especialidad', 'idAlumno', 'idEspecialidad')
					->withPivot('id', 'semestreInicio');
	}

	public function generacions()
	{
		return $this->belongsToMany(Generacion::class, 'alumno_generacion', 'idAlumno', 'idGeneracion')
					->withPivot('id', 'semestreInicial');
	}

	public function grupo_semestres()
	{
		return $this->belongsToMany(GrupoSemestre::class, 'alumno_grupo_semestre', 'idAlumno', 'idGrupoSemestre')
					->withPivot('id');
	}

	public function calificacions()
	{
		return $this->hasMany(Calificacion::class, 'idAlumno');
	}
}
