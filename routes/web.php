<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});


Route::middleware('auth')->group(function () {

    //RUTAS PARA EL ADMIN
    Route::middleware(['auth', 'role:1,2'])->group(function () {
        Route::get('/dashboard', function () { return view('admin.dashboard'); })->name('dashboard');
        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios');
        Route::get('/nueva-cotizacion', function () { return view('admin.cotizacion'); });//TEMPORAL*********************************************
    });

    //RUTAS PARA USUARIOS
    Route::middleware(['auth', 'role:3,4'])->group(function () { Route::get('/work', function () { return view('work'); });});//TEMPORAL*********************************************
});

require __DIR__.'/auth.php';
