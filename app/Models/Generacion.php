<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Generacion
 *
 * @property int $id
 * @property Carbon $anioIngreso
 * @property Carbon $anioEgreso
 *
 * @property Collection|Alumno[] $alumnos
 *
 * @package App\Models
 */
class Generacion extends Model
{
	protected $table = 'generacion';
	public $timestamps = false;

	protected $casts = [
		'fechaIngreso' => 'date',
		'fechaEgreso' => 'date'
	];

	protected $fillable = [
		'fechaIngreso',
		'fechaEgreso'
	];

	public function alumnos()
	{
		return $this->belongsToMany(Alumno::class, 'alumno_generacion', 'idGeneracion', 'idAlumno')
					->withPivot('id', 'semestreInicial');
	}
}
