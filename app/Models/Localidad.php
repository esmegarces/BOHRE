<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Localidad
 * 
 * @property int $id
 * @property string $nombre
 * @property int $codigoPostal
 * @property int $idMunicipio
 * 
 * @property Municipio $municipio
 * @property Collection|Direccion[] $direccions
 *
 * @package App\Models
 */
class Localidad extends Model
{
	protected $table = 'localidad';
	public $timestamps = false;

	protected $casts = [
		'codigoPostal' => 'int',
		'idMunicipio' => 'int'
	];

	protected $fillable = [
		'nombre',
		'codigoPostal',
		'idMunicipio'
	];

	public function municipio()
	{
		return $this->belongsTo(Municipio::class, 'idMunicipio');
	}

	public function direccions()
	{
		return $this->hasMany(Direccion::class, 'idLocalidad');
	}
}
