<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AlumnoCiclo
 * 
 * @property int $id
 * @property int $semestreCursado
 * @property int $idAlumno
 * @property int $idCicloEscolar
 * 
 * @property Alumno $alumno
 * @property CicloEscolar $ciclo_escolar
 *
 * @package App\Models
 */
class AlumnoCiclo extends Model
{
	protected $table = 'alumno_ciclo';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int',
		'semestreCursado' => 'int',
		'idAlumno' => 'int',
		'idCicloEscolar' => 'int'
	];

	protected $fillable = [
		'semestreCursado',
		'idAlumno',
		'idCicloEscolar'
	];

	public function alumno()
	{
		return $this->belongsTo(Alumno::class, 'idAlumno');
	}

	public function ciclo_escolar()
	{
		return $this->belongsTo(CicloEscolar::class, 'idCicloEscolar');
	}
}
