<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ClasesAlumnoVw
 * 
 * @property int $id
 * @property string $nombre
 * @property string $sexo
 * @property string $nia
 * @property string $situacion
 * @property string $ciclo_es
 * @property string $generacion
 * @property string|null $esp
 * @property string $grupo
 * @property array|null $asignaturas
 *
 * @package App\Models
 */
class ClasesAlumnoVw extends Model
{
	protected $table = 'clases_alumno_vw';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int',
		'asignaturas' => 'json'
	];

	protected $fillable = [
		'id',
		'nombre',
		'sexo',
		'nia',
		'situacion',
		'ciclo_es',
		'generacion',
		'esp',
		'grupo',
		'asignaturas'
	];
}
