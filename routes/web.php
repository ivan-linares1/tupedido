<?php

use App\Http\Controllers\ArticuloController;
use App\Http\Controllers\ArticuloControlles;
use App\Http\Controllers\CotizacionesController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});


Route::middleware('auth')->group(function () {

    //RUTAS PARA EL ADMIN
    Route::middleware(['auth', 'role:1,2'])->group(function () {
        Route::get('/Dashboard', function () { return view('admin.dashboard'); })->name('dashboard');
        Route::get('/Usuarios', [UsuarioController::class, 'index'])->name('usuarios');
        Route::get('/Cotizaciones', [CotizacionesController::class, 'index'])->name('cotizaciones');
        Route::get('/cliente/{cardCode}/direcciones', [CotizacionesController::class, 'ObtenerDirecciones'])->name('ObtenerDirecciones');
        Route::get('/CatalogosArticulos', [ArticuloController::class, 'index'])->name('articulos');
    });

    //RUTAS PARA USUARIOS
    Route::middleware(['auth', 'role:3,4'])->group(function () { 
    });
    
});

require __DIR__.'/auth.php';
