<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AsignaturasEspecialidadesView
 * 
 * @property int $id
 * @property string $asignatura
 * @property string $tipo
 * @property int|null $idEspecialidad
 * @property string|null $especialidad
 * @property int $semestre
 *
 * @package App\Models
 */
class AsignaturasEspecialidadesView extends Model
{
	protected $table = 'asignaturas_especialidades_view';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int',
		'idEspecialidad' => 'int',
		'semestre' => 'int'
	];

	protected $fillable = [
		'id',
		'asignatura',
		'tipo',
		'idEspecialidad',
		'especialidad',
		'semestre'
	];
}
