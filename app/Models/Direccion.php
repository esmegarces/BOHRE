<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Direccion
 *
 * @property int $id
 * @property int $numeroCasa
 * @property string $calle
 * @property int $idLocalidad
 *
 * @property Localidad $localidad
 * @property Collection|Persona[] $personas
 *
 * @package App\Models
 */
class Direccion extends Model
{
    use HasFactory;
	protected $table = 'direccion';
	public $timestamps = false;

	protected $casts = [
		'numeroCasa' => 'int',
		'idLocalidad' => 'int'
	];

	protected $fillable = [
		'numeroCasa',
		'calle',
		'idLocalidad'
	];

	public function localidad()
	{
		return $this->belongsTo(Localidad::class, 'idLocalidad');
	}

	public function personas()
	{
		return $this->hasMany(Persona::class, 'idDireccion');
	}
}
