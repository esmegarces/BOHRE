<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Clase
 * 
 * @property int $id
 * @property string $salonClase
 * @property int $idAsignatura
 * @property int $idDocente
 * @property int $idGrupoSemestre
 * @property int|null $idEspecialidad
 * 
 * @property Asignatura $asignatura
 * @property Docente $docente
 * @property GrupoSemestre $grupo_semestre
 * @property Especialidad|null $especialidad
 * @property Collection|Calificacion[] $calificacions
 *
 * @package App\Models
 */
class Clase extends Model
{
	protected $table = 'clase';
	public $timestamps = false;

	protected $casts = [
		'idAsignatura' => 'int',
		'idDocente' => 'int',
		'idGrupoSemestre' => 'int',
		'idEspecialidad' => 'int'
	];

	protected $fillable = [
		'salonClase',
		'idAsignatura',
		'idDocente',
		'idGrupoSemestre',
		'idEspecialidad'
	];

	public function asignatura()
	{
		return $this->belongsTo(Asignatura::class, 'idAsignatura');
	}

	public function docente()
	{
		return $this->belongsTo(Docente::class, 'idDocente');
	}

	public function grupo_semestre()
	{
		return $this->belongsTo(GrupoSemestre::class, 'idGrupoSemestre');
	}

	public function especialidad()
	{
		return $this->belongsTo(Especialidad::class, 'idEspecialidad');
	}

	public function calificacions()
	{
		return $this->hasMany(Calificacion::class, 'idClase');
	}
}
