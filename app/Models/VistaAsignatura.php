<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VistaAsignatura
 * 
 * @property int $idAsignatura
 * @property string $nombre
 * @property string $tipo
 * @property string $semestre
 * @property string $periodo
 * @property string $especialidad
 *
 * @package App\Models
 */
class VistaAsignatura extends Model
{
	protected $table = 'vista_asignaturas';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'idAsignatura' => 'int'
	];

	protected $fillable = [
		'idAsignatura',
		'nombre',
		'tipo',
		'semestre',
		'periodo',
		'especialidad'
	];
}
