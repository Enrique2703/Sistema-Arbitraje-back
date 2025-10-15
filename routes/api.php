<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpedienteController;
use App\Http\Controllers\ParticipeController;
use App\Http\Controllers\UsuarioController;
use App\Models\Expediente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);

Route::middleware(['session.timeout'])->group(function () {
    Route::apiResource('usuarios', UsuarioController::class);
    Route::apiResource('participes', ParticipeController::class);
    Route::apiResource('expedientes', ExpedienteController::class);
});
