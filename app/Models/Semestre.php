<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Semestre
 * 
 * @property int $id
 * @property int $numero
 * @property string $periodo
 * 
 * @property Collection|Grupo[] $grupos
 * @property Collection|PlanAsignatura[] $plan_asignaturas
 *
 * @package App\Models
 */
class Semestre extends Model
{
	protected $table = 'semestre';
	public $timestamps = false;

	protected $casts = [
		'numero' => 'int'
	];

	protected $fillable = [
		'numero',
		'periodo'
	];

	public function grupos()
	{
		return $this->belongsToMany(Grupo::class, 'grupo_semestre', 'idSemestre', 'idGrupo')
					->withPivot('id');
	}

	public function plan_asignaturas()
	{
		return $this->hasMany(PlanAsignatura::class, 'idSemestre');
	}
}
