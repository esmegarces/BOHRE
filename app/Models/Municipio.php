<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Municipio
 * 
 * @property int $id
 * @property string $nombre
 * 
 * @property Collection|Localidad[] $localidads
 *
 * @package App\Models
 */
class Municipio extends Model
{
	protected $table = 'municipio';
	public $timestamps = false;

	protected $fillable = [
		'nombre'
	];

	public function localidads()
	{
		return $this->hasMany(Localidad::class, 'idMunicipio');
	}
}
