<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AlumnosGruposView
 * 
 * @property int $idGrupoSemestre
 * @property string $grupo
 * @property int $semestre
 * @property string $periodoSemestre
 * @property array|null $alumnos
 *
 * @package App\Models
 */
class AlumnosGruposView extends Model
{
	protected $table = 'alumnos_grupos_view';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'idGrupoSemestre' => 'int',
		'semestre' => 'int',
		'alumnos' => 'json'
	];

	protected $fillable = [
		'idGrupoSemestre',
		'grupo',
		'semestre',
		'periodoSemestre',
		'alumnos'
	];
}
