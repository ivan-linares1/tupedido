<?php

use App\Http\Controllers\ArticuloController;
use App\Http\Controllers\Controller;
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
        Route::post('/CotizacionesGuardar', [CotizacionesController::class, 'GuardarCotizacion'])->name('cotizacionSave');
        
        //borrar cuando este en produccion*****************
        Route::get('/insertar-monedas', [UsuarioController::class, 'insertarMonedas'])->name('insertar.monedas');
    });

    //RUTAS PARA USUARIOS
    Route::middleware(['auth', 'role:3,4'])->group(function () { 
    });
    
});


// Grupo admin
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');

    // Ruta para obtener datos del cliente (AJAX)
    Route::get('/ocrd/show', [UsuarioController::class, 'getCliente'])->name('ocrd.show');
});



require __DIR__.'/auth.php';
