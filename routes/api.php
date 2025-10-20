<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// Routes de localidad
Route::get('/municipios', [\App\Http\Controllers\DireccionController::class, 'getMunicipios']);
Route::get('/localidades/{id}', [\App\Http\Controllers\DireccionController::class, 'getLocalidades']);


//Routes de usuario
//ids deben ser de person id
Route::post('/usuarios', [\App\Http\Controllers\UserController::class, 'store']);
Route::get('/usuarios', [\App\Http\Controllers\UserController::class, 'show']);
Route::get('/usuarios/deleted', [\App\Http\Controllers\UserController::class, 'showDeletes']);
Route::get('/usuarios/byRol', [\App\Http\Controllers\UserController::class, 'showByRol']);
Route::patch('/usuarios/restore/{id}', [\App\Http\Controllers\UserController::class, 'restore']);
Route::patch('/usuarios/{id}', [\App\Http\Controllers\UserController::class, 'update']);
Route::delete('/usuarios/{id}', [\App\Http\Controllers\UserController::class, 'destroy']);
Route::delete('/usuarios/delete/{id}', [\App\Http\Controllers\UserController::class, 'destroyPermanently']);

// operaciones sobre el alumno
Route::post('/alumno/{id}', [\App\Http\Controllers\AlumnoController::class, 'storeExtraData']);


Route::group(['prefix' => 'periodos'], function () {
    Route::get('/generaciones', [\App\Http\Controllers\PeriodosController::class, 'getGeneraciones']);
    Route::get('/gruposemestres', [\App\Http\Controllers\PeriodosController::class, 'getGrupoSemestre']);
});

Route::get('/especialidades', [\App\Http\Controllers\EspecialidadesController::class, 'index']);
Route::get('/semestres', [\App\Http\Controllers\SemestresController::class, 'index']);

Route::get('/asignaturas', [\App\Http\Controllers\AsignaturasController::class, 'index']);
Route::get('/asignaturas/{id}', [\App\Http\Controllers\AsignaturasController::class, 'show']);
Route::post('/asignaturas', [\App\Http\Controllers\AsignaturasController::class, 'store']);
Route::patch('/asignaturas/{id}', [\App\Http\Controllers\AsignaturasController::class, 'update']);
Route::delete('/asignaturas/{id}', [\App\Http\Controllers\AsignaturasController::class, 'destroy']);

Route::get('/gruposemestresinfo', [\App\Http\Controllers\GrupoSemestreInfoViewController::class, 'index']);
Route::get('/gruposemestresinfo/{id}', [\App\Http\Controllers\GrupoSemestreInfoViewController::class, 'showExtraInfo']);
