<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class GrupoSemestreInfoView
 * 
 * @property int $idGrupoSemestre
 * @property int $idGrupo
 * @property int $idSemestre
 * @property string $grupo
 * @property int $semestre
 * @property string $periodoSemestre
 * @property int $numeroAlumnos
 * @property int $numeroAsignaturas
 *
 * @package App\Models
 */
class GrupoSemestreInfoView extends Model
{
	protected $table = 'grupo_semestre_info_view';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'idGrupoSemestre' => 'int',
		'idGrupo' => 'int',
		'idSemestre' => 'int',
		'semestre' => 'int',
		'numeroAlumnos' => 'int',
		'numeroAsignaturas' => 'int'
	];

	protected $fillable = [
		'idGrupoSemestre',
		'idGrupo',
		'idSemestre',
		'grupo',
		'semestre',
		'periodoSemestre',
		'numeroAlumnos',
		'numeroAsignaturas'
	];
}
