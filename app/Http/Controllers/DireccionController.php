<?php

namespace App\Http\Controllers;

use App\Models\Municipio;
use Illuminate\Http\Request;

class DireccionController extends Controller
{
    public function getMunicipios()
    {
        $municipios = Municipio::all();

        if (!$municipios || $municipios->isEmpty()) {
            return response()->json(['message' => 'No se encontraron municipios.'], 404);
        }

        return response()->json(['message' => 'Municipios recuperados con exito.', 'data' => $municipios]);
    }

    /**
     * @param $id int del municipio para obtener sus localidades
     * @return \Illuminate\Http\JsonResponse respuesta en formato json
     */
    public function getLocalidades($id)
    {
        // Validar que el ID sea un número entero positivo
        if (!$id || !is_numeric($id) || $id <= 0) {
            return response()->json(['message' => 'El ID del municipio debe ser un número entero positivo.'], 400);
        }

        // buscar el municipio por su ID
        $municipio = Municipio::find($id);

        // Verificar si el municipio existe
        if (!$municipio) {
            return response()->json(['message' => 'Municipio no encontrado.'], 404);
        }

        // Obtener las localidades asociadas al municipio
        $localidades = $municipio->localidads()->get(['id', 'nombre']);

        // Verificar si se encontraron localidades
        if ($localidades->isEmpty()) {
            return response()->json(['message' => 'No se encontraron localidades para este municipio.'], 404);
        }

        return response()->json(['message' => 'Localidades recuperadas con exito.', 'data' => $localidades]);
    }
}
