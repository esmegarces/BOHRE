<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DireccionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PeriodosController;
use App\Http\Controllers\EspecialidadesController;
use App\Http\Controllers\AsignaturasController;
use App\Http\Controllers\GrupoSemestreInfoViewController;
use App\Http\Controllers\ClaseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocenteMateriasController;

// Rutas públicas
Route::post('login', [AuthController::class, 'login']);
Route::get('test-token', [AuthController::class, 'testToken']);

// Rutas protegidas con JWT
Route::middleware('auth:api')->group(function () {

    // Ruta para obtener usuario autenticado
    Route::get('me', [AuthController::class, 'me']);

    // Rutas solo para admin
    Route::middleware('role:ADMIN')->group(function () {

        Route::get('/dashboard/estadisticas', [DashboardController::class, 'obtenerEstadisticas']);

        // Routes de localidad
        Route::get('/municipios', [DireccionController::class, 'getMunicipios']);
        Route::get('/localidades/{id}', [DireccionController::class, 'getLocalidades']);

        //Routes de usuario
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

    });

    Route::middleware('role:DOCENTE')->group(function () {
        Route::prefix('docentes')->group(function () {
            Route::get('{idPerson}/materias', [DocenteMateriasController::class, 'obtenerMateriasPorDocente']);

        });
        Route::prefix('clases')->group(function () {
            Route::get('{idClase}/detalle', [DocenteMateriasController::class, 'obtenerDetalleClase']);
            Route::put('{idClase}/calificaciones', [DocenteMateriasController::class, 'guardarCalificaciones']);
        });

    });

});


