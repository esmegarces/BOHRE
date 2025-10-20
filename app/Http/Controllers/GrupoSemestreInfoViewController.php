<?php

namespace App\Http\Controllers;

use App\Models\AlumnosGruposView;
use App\Models\GrupoSemestreInfoView;
use App\Models\GruposSemestresAsignaturasAlumnosCalificacionesView;
use App\Models\VistaGruposSemestresAsignaturasAlumnosCalificacione;
use Illuminate\Http\Request;

class GrupoSemestreInfoViewController extends Controller
{
    public function index()
    {
        $gruposSemestreInfo = GrupoSemestreInfoView::paginate(15);

        if ($gruposSemestreInfo->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron registros de grupos semestre info.'
            ], 404);
        }

        return response()->json(['message' => 'informacion recuperada con exito', 'data' => $gruposSemestreInfo]);
    }

    public function showExtraInfo(int $id)
    {

        $grupoSemestreInfo = AlumnosGruposView::where('idGrupoSemestre', $id)->first();

        if (!$grupoSemestreInfo) {
            return response()->json([
                'message' => 'No se encontró información para el idGrupoSemestre proporcionado.'
            ], 404);
        }

        return response()->json(['message' => 'informacion recuperada con exito', 'data' => $grupoSemestreInfo]);

    }
}
