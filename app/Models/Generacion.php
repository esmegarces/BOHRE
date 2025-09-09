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
 * @property bool $activa
 * 
 * @property Collection|Alumno[] $alumnos
 *
 * @package App\Models
 */
class Generacion extends Model
{
	protected $table = 'generacion';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int',
		'anioIngreso' => 'datetime',
		'anioEgreso' => 'datetime',
		'activa' => 'bool'
	];

	protected $fillable = [
		'anioIngreso',
		'anioEgreso',
		'activa'
	];

	public function alumnos()
	{
		return $this->belongsToMany(Alumno::class, 'alumno_generacion', 'idGeneracion', 'idAlumno')
					->withPivot('id', 'semestreInicial');
	}
}
