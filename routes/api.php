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
//Routes de usuario
Route::post('/usuario',[\App\Http\Controllers\UserController::class,'store']);
Route::get('/usuarios',[\App\Http\Controllers\UserController::class,'show']);
Route::get('/usuario',[\App\Http\Controllers\UserController::class,'showByRol']);
Route::patch('/usuario/{id}',[\App\Http\Controllers\UserController::class,'update']);
Route::delete('/usuario/{id}',[\App\Http\Controllers\UserController::class,'destroy']);

// operaciones sobre el alumno
Route::post('/alumno/{id}',[\App\Http\Controllers\AlumnoController::class,'storeExtraData']);
