<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAsignaturaRequest;
use App\Http\Requests\UpdateAsignaturaRequest;
use App\Models\Asignatura;
use Exception;
use Illuminate\Support\Facades\DB;

class AsignaturasController extends Controller
{
    public function index()
    {
        $asignaturas = Asignatura::join('plan_asignatura as pa', 'asignatura.id', '=', 'pa.idAsignatura')
            ->join('semestre as s', 'pa.idSemestre', '=', 's.id')
            ->leftJoin('especialidad as e', 'pa.idEspecilidad', '=', 'e.id')
            ->select(
                'asignatura.id as idAsignatura',
                'asignatura.nombre',
                'asignatura.tipo',
                'pa.idSemestre',
                's.numero as semestre',
                'pa.idEspecilidad as idEspecialidad',
                DB::raw("COALESCE(e.nombre, 'NO APLICA') as especialidad")
            )
            ->paginate(15);


        if ($asignaturas->isEmpty()) {
            return response()->json([
                'message' => 'No hay asignaturas registradas',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Asignaturas obtenidas correctamente',
            'data' => $asignaturas
        ]);
    }

    public function show(int $id)
    {
        $asignatura = Asignatura::join('plan_asignatura as pa', 'asignatura.id', '=', 'pa.idAsignatura')
            ->join('semestre as s', 'pa.idSemestre', '=', 's.id')
            ->leftJoin('especialidad as e', 'pa.idEspecilidad', '=', 'e.id')
            ->select(
                'asignatura.id as idAsignatura',
                'asignatura.nombre',
                'asignatura.tipo',
                'pa.idSemestre',
                's.numero as semestre',
                'pa.idEspecilidad as idEspecialidad',
                DB::raw("COALESCE(e.nombre, 'NO APLICA') as especialidad")
            )
            ->where('asignatura.id', $id)
            ->first();

        if (!$asignatura) {
            return response()->json([
                'message' => 'Asignatura no encontrada',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Asignatura obtenida correctamente',
            'data' => $asignatura
        ]);
    }

    public function store(StoreAsignaturaRequest $request)
    {
        try {

            $query = DB::transaction(function () use ($request) {
                $asignatura = Asignatura::create([
                    'nombre' => mb_strtoupper($request->nombre),
                    'tipo' => $request->tipo,
                ]);

                $asignatura->plan_asignaturas()->create([
                    'idSemestre' => $request->idSemestre,
                    'idEspecilidad' => $request->idEspecialidad,
                ]);

                return Asignatura::join('plan_asignatura as pa', 'asignatura.id', '=', 'pa.idAsignatura')
                    ->join('semestre as s', 'pa.idSemestre', '=', 's.id')
                    ->leftJoin('especialidad as e', 'pa.idEspecilidad', '=', 'e.id')
                    ->select(
                        'asignatura.id as idAsignatura',
                        'asignatura.nombre',
                        'asignatura.tipo',
                        'pa.idSemestre',
                        's.numero as semestre',
                        'pa.idEspecilidad as idEspecialidad',
                        DB::raw("COALESCE(e.nombre, 'NO APLICA') as especialidad")
                    )->where('asignatura.id', $asignatura->id)->first();

            });

            return response()->json([
                'message' => 'Asignatura creada correctamente',
                'data' => $query
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al registrar la asignatura',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    public function update(UpdateAsignaturaRequest $request, $id)
    {

        $asignatura = Asignatura::find($id);

        if (!$asignatura) {
            return response()->json([
                'message' => 'Asignatura no encontrada',
                'data' => null
            ], 404);
        }


        try {

            $query = DB::transaction(function () use ($request, $asignatura) {

                $payload = $request->only(['nombre', 'tipo']);
                if (isset($payload['nombre'])) {
                    $payload['nombre'] = mb_strtoupper($payload['nombre']);
                }
                $asignatura->update($payload);

                if ($request->hasAny(['idSemestre', 'idEspecialidad'])) {
                    if ($request->hasAny(['idSemestre', 'idEspecialidad'])) {
                        $plan = $asignatura->plan_asignaturas()->first();
                        if ($plan) {
                            $plan->update([
                                'idSemestre' => $request->input('idSemestre', $plan->idSemestre),
                                'idEspecilidad' => $request->input('idEspecialidad', $plan->idEspecilidad),
                            ]);
                        }
                    }
                }

                return Asignatura::join('plan_asignatura as pa', 'asignatura.id', '=', 'pa.idAsignatura')
                    ->join('semestre as s', 'pa.idSemestre', '=', 's.id')
                    ->leftJoin('especialidad as e', 'pa.idEspecilidad', '=', 'e.id')
                    ->select(
                        'asignatura.id as idAsignatura',
                        'asignatura.nombre',
                        'asignatura.tipo',
                        'pa.idSemestre',
                        's.numero as semestre',
                        'pa.idEspecilidad as idEspecialidad',
                        'e.nombre as especialidad',
                        DB::raw("COALESCE(e.nombre, 'NO APLICA') as especialidad")
                    )->where('asignatura.id', $asignatura->id)->first();

            });

            return response()->json([
                'message' => 'Asignatura actualizada correctamente',
                'data' => $query
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar la asignatura',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    public function destroy($id)
    {
        $asignatura = Asignatura::find($id);

        if (!$asignatura) {
            return response()->json([
                'message' => 'Asignatura no encontrada',
                'data' => null
            ], 404);
        }

        try {
            DB::transaction(function () use ($asignatura) {
                $asignatura->plan_asignaturas()->delete();
                $asignatura->clases()->delete();
                $asignatura->delete();
            });

            return response()->json([
                'message' => 'Asignatura eliminada correctamente',
                'data' => null
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar la asignatura',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
