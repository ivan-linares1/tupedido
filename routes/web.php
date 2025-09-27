<?php

use App\Http\Controllers\ArticuloController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CotizacionesController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VendedorController;
use App\Http\Controllers\OslpController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('auth')->group(function () {

    // RUTAS PARA EL ADMIN
    Route::middleware(['auth', 'role:1,2'])->group(function () {
        Route::get('/Dashboard', function () { 
            return view('admin.dashboard'); 
        })->name('dashboard');

        Route::get('/Usuarios', [UsuarioController::class, 'index'])->name('usuarios');
        Route::get('/Cotizaciones', [CotizacionesController::class, 'index'])->name('cotizaciones');
        Route::get('/cliente/{cardCode}/direcciones', [CotizacionesController::class, 'ObtenerDirecciones'])->name('ObtenerDirecciones');
        Route::get('/CatalogosArticulos', [ArticuloController::class, 'index'])->name('articulos');
        Route::post('/CotizacionesGuardar', [CotizacionesController::class, 'GuardarCotizacion'])->name('cotizacionSave');
        
        // Borrar cuando este en producción *****************
        Route::get('/insertar-monedas', [UsuarioController::class, 'insertarMonedas'])->name('insertar.monedas');

        // ✅ Ruta Catálogo de Vendedores (solo admin)
        Route::get('/admin/catalogo-vendedores', [VendedorController::class, 'index'])
            ->name('admin.catalogo.vendedores');
    });

    // RUTAS PARA USUARIOS NORMALES Y VENDEDORES
    Route::middleware(['auth', 'role:3,4'])->group(function () { 
        // aquí puedes agregar rutas para usuarios normales y vendedores si las necesitas
    });
    
});

// Grupo admin (recursos con prefijo admin)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');

    // Ruta para obtener datos del cliente (AJAX) por cardCode
    Route::get('/ocrd/{cardCode}', [UsuarioController::class, 'getCliente'])->name('ocrd.show');

    // Ruta para obtener datos del vendedor (AJAX) por slpCode
    Route::get('/oslp/{slpCode}', [UsuarioController::class, 'show'])->name('oslp.show');
});

require __DIR__.'/auth.php';
