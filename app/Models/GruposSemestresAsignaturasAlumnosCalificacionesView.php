<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class GruposSemestresAsignaturasAlumnosCalificacionesView
 * 
 * @property int $idGrupoSemestre
 * @property int $idGrupo
 * @property int $idSemestre
 * @property string $grupo
 * @property int $semestre
 * @property string $periodoSemestre
 * @property array|null $asignaturas
 *
 * @package App\Models
 */
class GruposSemestresAsignaturasAlumnosCalificacionesView extends Model
{
	protected $table = 'grupos_semestres_asignaturas_alumnos_calificaciones_view';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'idGrupoSemestre' => 'int',
		'idGrupo' => 'int',
		'idSemestre' => 'int',
		'semestre' => 'int',
		'asignaturas' => 'json'
	];

	protected $fillable = [
		'idGrupoSemestre',
		'idGrupo',
		'idSemestre',
		'grupo',
		'semestre',
		'periodoSemestre',
		'asignaturas'
	];
}
