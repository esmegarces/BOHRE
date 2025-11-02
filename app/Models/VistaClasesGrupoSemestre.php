<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VistaClasesGrupoSemestre extends Model
{
    protected $table = 'vista_clases_grupo_semestre';
    public $incrementing = false;
    public $timestamps = false;

    protected $casts = [
        'idClase' => 'int',
        'anio' => 'int',
        'idGrupoSemestre' => 'int',
        'semestre' => 'int',
        'idAsignatura' => 'int',
        'idEspecialidad' => 'int',
        'idDocente' => 'int',
        'alumnosInscritos' => 'int',
    ];

    protected $fillable = [
        'idClase',
        'anio',
        'idGrupoSemestre',
        'semestre',
        'grupo',
        'idAsignatura',
        'nombreAsignatura',
        'tipoAsignatura',
        'idEspecialidad',
        'especialidad',
        'idDocente',
        'nombreDocente',
        'alumnosInscritos',
    ];
}
