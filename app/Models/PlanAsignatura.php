<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PlanAsignatura
 * 
 * @property int $id
 * @property int $idAsignatura
 * @property int $idSemestre
 * @property int|null $idEspecilidad
 * 
 * @property Asignatura $asignatura
 * @property Semestre $semestre
 * @property Especialidad|null $especialidad
 *
 * @package App\Models
 */
class PlanAsignatura extends Model
{
	protected $table = 'plan_asignatura';
	public $timestamps = false;

	protected $casts = [
		'idAsignatura' => 'int',
		'idSemestre' => 'int',
		'idEspecilidad' => 'int'
	];

	protected $fillable = [
		'idAsignatura',
		'idSemestre',
		'idEspecilidad'
	];

	public function asignatura()
	{
		return $this->belongsTo(Asignatura::class, 'idAsignatura');
	}

	public function semestre()
	{
		return $this->belongsTo(Semestre::class, 'idSemestre');
	}

	public function especialidad()
	{
		return $this->belongsTo(Especialidad::class, 'idEspecilidad');
	}
}
