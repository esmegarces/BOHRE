<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Clase
 *
 * @property int $id
 * @property string $salonClase
 * @property int $idAsignatura
 * @property int|null $idDocente
 * @property int $idGrupoSemestre
 * @property int|null $idEspecialidad
 * @property date $fechaInicio
 * @property date|null $fechaFin
 *
 * @property Asignatura $asignatura
 * @property Docente|null $docente
 * @property GrupoSemestre $grupo_semestre
 * @property Especialidad|null $especialidad
 * @property Collection|Calificacion[] $calificacions
 *
 * @package App\Models
 */
class Clase extends Model
{
    protected $table = 'clase';
    public $timestamps = false;

    protected $casts = [
        'idAsignatura' => 'int',
        'idDocente' => 'int',
        'idGrupoSemestre' => 'int',
        'idEspecialidad' => 'int',
        'anio' => 'int',
    ];

    protected $fillable = [
        'idAsignatura',
        'idDocente',
        'idGrupoSemestre',
        'idEspecialidad',
        'anio',
    ];

    public function asignatura()
    {
        return $this->belongsTo(Asignatura::class, 'idAsignatura');
    }

    public function docente()
    {
        return $this->belongsTo(Docente::class, 'idDocente');
    }

    public function grupoSemestre()
    {
        return $this->belongsTo(GrupoSemestre::class, 'idGrupoSemestre');
    }

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class, 'idEspecialidad');
    }

    // Relación con Calificaciones
    public function calificacions()
    {
        return $this->hasMany(Calificacion::class, 'idClase');
    }
}
