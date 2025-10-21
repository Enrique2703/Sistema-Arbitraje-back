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

Route::get('/expedientes/participes', function () {
    return view('expedientes.participes.index');
})->name('expedientes.participes.index');