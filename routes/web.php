<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
})->name('login');

Route::get('/login', function () {
    return view('auth.login');
})->name('login.show');

Route::get('/usuarios', function () {
    return view('usuarios.index');
})->name('usuarios.index');

Route::get('/participes', function () {
    return view('participes.index');
})->name('participes.index');

Route::get('/expedientes', function () {
    return view('expedientes.index');
})->name('expedientes.index');

Route::get('/auditoria', function () {
    return view('auditoria.index');
})->name('auditoria.index');

Route::get('/calculadora', function () {
    return view('calculadora.index');
})->name('calculadora.index');

Route::get('/solicitudes', function () {
    return view('solicitudes.index');
})->name('solicitudes.index');

Route::get('/expedientes/participes', function () {
    return view('expedientes.participes.index');
})->name('expedientes.participes.index');

Route::get('/expedientes/participes/seguimiento', function () {
    return view('expedientes.participes.se_tramite');
})->name('expedientes.participes.seguimiento');

Route::get('/expedientes/documentos', function () {
    return view('expedientes.documentos');
})->name('expedientes.documentos');

Route::get('/expedientes/historial', function () {
    return view('expedientes.historial');
})->name('expedientes.historial');