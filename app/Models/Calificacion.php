<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Calificacion
 * 
 * @property int $id
 * @property int $momento1
 * @property int $momento2
 * @property int $momento3
 * @property int $idClase
 * @property int $idAlumno
 * 
 * @property Clase $clase
 * @property Alumno $alumno
 *
 * @package App\Models
 */
class Calificacion extends Model
{
	protected $table = 'calificacion';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int',
		'momento1' => 'int',
		'momento2' => 'int',
		'momento3' => 'int',
		'idClase' => 'int',
		'idAlumno' => 'int'
	];

	protected $fillable = [
		'momento1',
		'momento2',
		'momento3',
		'idClase',
		'idAlumno'
	];

	public function clase()
	{
		return $this->belongsTo(Clase::class, 'idClase');
	}

	public function alumno()
	{
		return $this->belongsTo(Alumno::class, 'idAlumno');
	}
}
