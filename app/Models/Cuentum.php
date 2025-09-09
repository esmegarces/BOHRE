<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Cuentum
 * 
 * @property int $id
 * @property string $correo
 * @property string $contrasena
 * @property string $rol
 * 
 * @property Collection|Persona[] $personas
 *
 * @package App\Models
 */
class Cuentum extends Model
{
	protected $table = 'cuenta';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int'
	];

	protected $fillable = [
		'correo',
		'contrasena',
		'rol'
	];

	public function personas()
	{
		return $this->hasMany(Persona::class, 'idCuenta');
	}
}
