<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Docente
 * 
 * @property int $id
 * @property string $cedulaProfesional
 * @property int $numeroExpediente
 * @property int $idPersona
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Persona $persona
 * @property Collection|Clase[] $clases
 *
 * @package App\Models
 */
class Docente extends Model
{
	protected $table = 'docente';

	protected $casts = [
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
