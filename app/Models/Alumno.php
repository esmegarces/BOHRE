<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Alumno
 * 
 * @property int $id
 * @property string $nia
 * @property int $numeroLista
 * @property string $situacion
 * @property int $idPersona
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
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int',
		'numeroLista' => 'int',
		'idPersona' => 'int'
	];

	protected $fillable = [
		'nia',
		'numeroLista',
		'situacion',
		'idPersona'
	];

	public function persona()
	{
		return $this->belongsTo(Persona::class, 'idPersona');
	}

	public function alumno_ciclos()
	{
		return $this->hasMany(AlumnoCiclo::class, 'idAlumno');
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
