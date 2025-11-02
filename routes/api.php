<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DireccionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PeriodosController;
use App\Http\Controllers\EspecialidadesController;
use App\Http\Controllers\AsignaturasController;
use App\Http\Controllers\GrupoSemestreInfoViewController;
use App\Http\Controllers\ClaseController;

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
Route::get('/municipios', [DireccionController::class, 'getMunicipios']);
Route::get('/localidades/{id}', [DireccionController::class, 'getLocalidades']);


//Routes de usuario
//ids deben ser de person id
Route::post('/usuarios', [UserController::class, 'store']);
Route::get('/usuarios', [UserController::class, 'show']);
Route::get('/usuarios/deleted', [UserController::class, 'showDeletes']);
Route::get('/usuarios/{rol}/{id}', [UserController::class, 'retrieveByRol']);
Route::patch('/usuarios/restore/{id}', [UserController::class, 'restore']);
Route::patch('/usuarios/{id}', [UserController::class, 'update']);
Route::delete('/usuarios/{id}', [UserController::class, 'destroy']);
Route::delete('/usuarios/delete/{id}', [UserController::class, 'destroyPermanently']);
Route::get('/usuarios/docentes', [UserController::class, 'getDocentes']);
Route::get('/exportar-personas', [UserController::class, 'exportExcel']);
Route::patch('/alumno/asignarEspecialidad', [UserController::class, 'asignarEspecialidad']);


Route::get('periodos/generaciones', [PeriodosController::class, 'getGeneraciones']);
Route::post('periodos/generaciones', [PeriodosController::class, 'createGeneracion']);
Route::get('periodos/generacionesAlumnos', [PeriodosController::class, 'getGeneracionesWithAlumnos']);
Route::get('periodos/generacionesAlumnos/{id}', [PeriodosController::class, 'getAlumnosGeneraciones']);
Route::get('periodos/gruposemestres', [PeriodosController::class, 'getGrupoSemestre']);
Route::get('periodos/semestres', [PeriodosController::class, 'getSemestres']);
Route::get('periodos/semestresRAW', [PeriodosController::class, 'getSemestresRAW']);
Route::patch('periodos/semestres', [PeriodosController::class, 'updateSemestres']);


Route::get('/especialidades', [EspecialidadesController::class, 'index']);
Route::get('/especialidades/{id}', [EspecialidadesController::class, 'getDetailsCalificationsByEspecialidad']);
Route::post('/especialidades', [EspecialidadesController::class, 'store']);
Route::put('/especialidades/{id}', [EspecialidadesController::class, 'update']);
Route::get('/especialidades/asignaturas/{id}', [EspecialidadesController::class, 'getAsignaturasByEspecialidad']);


Route::get('/asignaturas', [AsignaturasController::class, 'index']);
Route::get('/asignaturas/{id}', [AsignaturasController::class, 'show']);
Route::post('/asignaturas', [AsignaturasController::class, 'store']);
Route::patch('/asignaturas/{id}', [AsignaturasController::class, 'update']);
Route::delete('/asignaturas/{id}', [AsignaturasController::class, 'destroy']);


Route::get('/gruposemestres/details', [GrupoSemestreInfoViewController::class, 'index']);
Route::get('/gruposemestres/details/{id}', [GrupoSemestreInfoViewController::class, 'showExtraInfo']);

Route::post('/clases/generar', [ClaseController::class, 'generar']);
Route::patch('/clases/{idClase}/asignar-docente', [ClaseController::class, 'asignarDocente']);
Route::get('/clases/{idClase}/calificaciones', [ClaseController::class, 'getCalificaciones']);
Route::get('/clases/{idgrupoSemestre}/download/calificaciones', [ClaseController::class, 'getExcelCalificaciones']);
Route::get('/clases/{numeroSemestre}/{idEspecialidad}/download/calificacionesEsp', [ClaseController::class, 'getExcelCalificacionesEsp']);

