<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AlumnoGrupoSemestre
 * 
 * @property int $id
 * @property int $idAlumno
 * @property int $idGrupoSemestre
 * 
 * @property Alumno $alumno
 * @property GrupoSemestre $grupo_semestre
 *
 * @package App\Models
 */
class AlumnoGrupoSemestre extends Model
{
	protected $table = 'alumno_grupo_semestre';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int',
		'idAlumno' => 'int',
		'idGrupoSemestre' => 'int'
	];

	protected $fillable = [
		'idAlumno',
		'idGrupoSemestre'
	];

	public function alumno()
	{
		return $this->belongsTo(Alumno::class, 'idAlumno');
	}

	public function grupo_semestre()
	{
		return $this->belongsTo(GrupoSemestre::class, 'idGrupoSemestre');
	}
}
