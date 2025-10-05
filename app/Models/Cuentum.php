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
 * Class Cuentum
 * 
 * @property int $id
 * @property string $correo
 * @property string $contrasena
 * @property string $rol
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Persona[] $personas
 *
 * @package App\Models
 */
class Cuentum extends Model
{
	use SoftDeletes;
	protected $table = 'cuenta';

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
