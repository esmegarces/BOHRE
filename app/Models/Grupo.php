<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Grupo
 * 
 * @property int $id
 * @property string $prefijo
 * 
 * @property Collection|Semestre[] $semestres
 *
 * @package App\Models
 */
class Grupo extends Model
{
	protected $table = 'grupo';
	public $timestamps = false;

	protected $fillable = [
		'prefijo'
	];

	public function semestres()
	{
		return $this->belongsToMany(Semestre::class, 'grupo_semestre', 'idGrupo', 'idSemestre')
					->withPivot('id');
	}
}
