<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Docente
 * 
 * @property int $id
 * @property string $cedulaProfesional
 * @property int $numeroExpediente
 * @property int $idPersona
 * 
 * @property Persona $persona
 * @property Collection|Clase[] $clases
 *
 * @package App\Models
 */
class Docente extends Model
{
	protected $table = 'docente';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int',
		'numeroExpediente' => 'int',
		'idPersona' => 'int'
	];

	protected $fillable = [
		'cedulaProfesional',
		'numeroExpediente',
		'idPersona'
	];

	public function persona()
	{
		return $this->belongsTo(Persona::class, 'idPersona');
	}

	public function clases()
	{
		return $this->hasMany(Clase::class, 'idDocente');
	}
}
