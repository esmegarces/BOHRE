<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AsignaturasEspecialidadesVw
 * 
 * @property int $id
 * @property string $asignatura
 * @property string $tipo
 * @property string|null $especialidad
 * @property int $semestre
 *
 * @package App\Models
 */
class AsignaturasEspecialidadesVw extends Model
{
	protected $table = 'asignaturas_especialidades_vw';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int',
		'semestre' => 'int'
	];

	protected $fillable = [
		'id',
		'asignatura',
		'tipo',
		'especialidad',
		'semestre'
	];
}
