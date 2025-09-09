<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class GrupoSemestre
 * 
 * @property int $id
 * @property int $idGrupo
 * @property int $idSemestre
 * 
 * @property Grupo $grupo
 * @property Semestre $semestre
 * @property Collection|Alumno[] $alumnos
 * @property Collection|Clase[] $clases
 *
 * @package App\Models
 */
class GrupoSemestre extends Model
{
	protected $table = 'grupo_semestre';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int',
		'idGrupo' => 'int',
		'idSemestre' => 'int'
	];

	protected $fillable = [
		'idGrupo',
		'idSemestre'
	];

	public function grupo()
	{
		return $this->belongsTo(Grupo::class, 'idGrupo');
	}

	public function semestre()
	{
		return $this->belongsTo(Semestre::class, 'idSemestre');
	}

	public function alumnos()
	{
		return $this->belongsToMany(Alumno::class, 'alumno_grupo_semestre', 'idGrupoSemestre', 'idAlumno')
					->withPivot('id');
	}

	public function clases()
	{
		return $this->hasMany(Clase::class, 'idGrupoSemestre');
	}
}
