<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpedienteController;
use App\Http\Controllers\ParticipeController;
use App\Http\Controllers\ParticipeDocumentoController;
use App\Http\Controllers\ParticipeDocumentoArchivoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\DocumentoController;
use App\Models\Expediente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);
Route::get('/expedientes/participes', [ExpedienteController::class, 'indexParticipes']);
Route::get('/expedientes/participes/documentos', [DocumentoController::class, 'getDocumentosPorExpediente']);

Route::middleware(['session.timeout'])->group(function () {
    Route::apiResource('usuarios', UsuarioController::class);
    Route::get('participes/export', [ParticipeController::class, 'exportToExcel']);
    Route::apiResource('participes', ParticipeController::class);
    Route::apiResource('participe-documentos', ParticipeDocumentoController::class);

    // Rutas para archivos de documentos
    Route::apiResource('participe-documento-archivos', ParticipeDocumentoArchivoController::class);
    Route::get('participe-documento-archivos/{id}/download', [ParticipeDocumentoArchivoController::class, 'download'])
        ->name('participe-documento-archivos.download');
    Route::apiResource('expedientes', ExpedienteController::class);
    Route::apiResource('participe-documentos', ParticipeDocumentoController::class);
});
