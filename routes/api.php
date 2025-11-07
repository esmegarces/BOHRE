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
use App\Http\Controllers\BoletaController;

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

        Route::prefix('direcciones')->group(function () {
            Route::get('municipios', [DireccionController::class, 'getMunicipios']);
            Route::get('localidades/{id}', [DireccionController::class, 'getLocalidades']);
        });

        Route::prefix('usuarios')->group(function () {
            //Routes de usuario
            Route::post('', [UserController::class, 'store']);
            Route::get('', [UserController::class, 'show']);
            Route::get('deleted', [UserController::class, 'showDeletes']);
            Route::get('{rol}/{id}', [UserController::class, 'retrieveByRol']);
            Route::patch('restore/{id}', [UserController::class, 'restore']);
            Route::patch('{id}', [UserController::class, 'update']);
            Route::delete('{id}', [UserController::class, 'destroy']);
            Route::delete('delete/{id}', [UserController::class, 'destroyPermanently']);
            Route::get('docentes', [UserController::class, 'getDocentes']);
            Route::patch('/alumnos/asignarEspecialidad', [UserController::class, 'asignarEspecialidad']);
        });

        Route::prefix('periodos')->group(function () {
            Route::get('generaciones', [PeriodosController::class, 'getGeneraciones']);
            Route::post('generaciones', [PeriodosController::class, 'createGeneracion']);
            Route::get('generacionesAlumnos', [PeriodosController::class, 'getGeneracionesWithAlumnos']);
            Route::get('generacionesAlumnos/{id}', [PeriodosController::class, 'getAlumnosGeneraciones']);
            Route::get('gruposemestres', [PeriodosController::class, 'getGrupoSemestre']);
            Route::get('semestres', [PeriodosController::class, 'getSemestres']);
            Route::get('semestresRAW', [PeriodosController::class, 'getSemestresRAW']);
            Route::patch('semestres', [PeriodosController::class, 'updateSemestres']);
        });

        Route::prefix('especialidades')->group(function () {
            Route::get('', [EspecialidadesController::class, 'index']);
            Route::get('{id}', [EspecialidadesController::class, 'getDetailsCalificationsByEspecialidad']);
            Route::post('', [EspecialidadesController::class, 'store']);
            Route::put('{id}', [EspecialidadesController::class, 'update']);
            Route::get('asignaturas/{id}', [EspecialidadesController::class, 'getAsignaturasByEspecialidad']);
        });

        Route::prefix('asignaturas')->group(function () {
            Route::get('', [AsignaturasController::class, 'index']);
            Route::get('{id}', [AsignaturasController::class, 'show']);
            Route::post('', [AsignaturasController::class, 'store']);
            Route::patch('{id}', [AsignaturasController::class, 'update']);
            Route::delete('{id}', [AsignaturasController::class, 'destroy']);
        });


        Route::prefix('clases')->group(function () {
            Route::get('details', [GrupoSemestreInfoViewController::class, 'index']);
            Route::get('details/{id}', [GrupoSemestreInfoViewController::class, 'showExtraInfo']);
            Route::post('generar', [ClaseController::class, 'generar']);
            Route::patch('{idClase}/asignar-docente', [ClaseController::class, 'asignarDocente']);
            Route::get('{idClase}/calificaciones', [ClaseController::class, 'getCalificaciones']);
            Route::get('{idgrupoSemestre}/download/calificaciones', [ClaseController::class, 'getExcelCalificaciones']);
            Route::get('{numeroSemestre}/{idEspecialidad}/download/calificacionesEsp', [ClaseController::class, 'getExcelCalificacionesEsp']);
        });

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

    Route::middleware('role:ALUMNO')->group(function () {
        Route::prefix('alumnos')->group(function () {
            Route::get('{idPerson}/semestres', [BoletaController::class, 'obtenerSemestresAlumno']);
            Route::get('{idPerson}/boleta/{idGrupoSemestre}', [BoletaController::class, 'generarBoleta']);
        });
    });
});
