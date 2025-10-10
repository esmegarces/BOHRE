<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Asignatura
 *
 * @property int $id
 * @property string $nombre
 * @property string $tipo
 *
 * @property Collection|Clase[] $clases
 * @property Collection|PlanAsignatura[] $plan_asignaturas
 *
 * @package App\Models
 */
class Asignatura extends Model
{
	protected $table = 'asignatura';
	public $timestamps = false;

	protected $fillable = [
		'nombre',
		'tipo'
	];

	public function clases()
	{
		return $this->hasMany(Clase::class, 'idAsignatura');
	}

	public function plan_asignaturas()
	{
		return $this->hasMany(PlanAsignatura::class, 'idAsignatura');
	}
}
