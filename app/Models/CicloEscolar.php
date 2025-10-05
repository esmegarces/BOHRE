<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CicloEscolar
 * 
 * @property int $id
 * @property Carbon $anioInicio
 * @property Carbon $anioFin
 * @property bool $activo
 * 
 * @property Collection|AlumnoCiclo[] $alumno_ciclos
 *
 * @package App\Models
 */
class CicloEscolar extends Model
{
	protected $table = 'ciclo_escolar';
	public $timestamps = false;

	protected $casts = [
		'anioInicio' => 'datetime',
		'anioFin' => 'datetime',
		'activo' => 'bool'
	];

	protected $fillable = [
		'anioInicio',
		'anioFin',
		'activo'
	];

	public function alumno_ciclos()
	{
		return $this->hasMany(AlumnoCiclo::class, 'idCicloEscolar');
	}
}
