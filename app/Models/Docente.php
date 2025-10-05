<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Docente
 * 
 * @property int $id
 * @property string $cedulaProfesional
 * @property int $numeroExpediente
 * @property int $idPersona
 * @property string|null $deleted_at
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
	use SoftDeletes;
	protected $table = 'docente';
	public $incrementing = false;

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
