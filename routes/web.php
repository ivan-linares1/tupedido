<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticuloController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\CotizacionesController;
use App\Http\Controllers\PedidosController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VendedorController;
use App\Http\Controllers\MarcaController;

Route::get('/', fn() => redirect()->route('dashboard'));

// RUTAS PROTEGIDAS POR AUTENTICACIÓN
Route::middleware('auth')->group(function () {
    //DASHBOARD
    Route::get('/Dashboard', fn() => view('admin.dashboard'))->name('dashboard');

   // COTIZACIONES (prefijo /Cotizaciones)
    Route::prefix('Cotizaciones')->group(function () {
        Route::get('/', [CotizacionesController::class, 'index'])->name('cotizaciones');
        Route::post('/Guardar', [CotizacionesController::class, 'GuardarCotizacion'])->name('cotizacionSave');
        Route::post('/GuardarPedido', [PedidosController::class, 'GuardarCotizacion'])->name('cotizacionSavePedido');
    });

    // Otras rutas relacionadas con cotizaciones
    Route::get('/NuevaCotizacion/{DocEntry?}', [CotizacionesController::class, 'NuevaCotizacion'])->name('NuevaCotizacion');
    Route::get('/cliente/{cardCode}/direcciones', [CotizacionesController::class, 'ObtenerDirecciones'])->name('ObtenerDirecciones');
    Route::get('/cotizacion/{id}', [CotizacionesController::class, 'detalles'])->name('detalles');
    Route::get('/cotizacion/pdf/{id}', [CotizacionesController::class, 'pdfCotizacion'])->name('cotizacion.pdf');

   //PEDIDOS (prefijo /Pedidos)
    Route::prefix('Pedidos')->group(function () {
        Route::get('/', [PedidosController::class, 'index'])->name('Pedidos');
    });
    Route::get('/NuevoPedido/{DocEntry?}', [PedidosController::class, 'NuevoPedido'])->name('NuevaPedido');
    Route::get('/Pedido/{id}', [PedidosController::class, 'detallesPedido'])->name('detallesP');
    Route::get('/Pedido/pdf/{id}', [PedidosController::class, 'pdfCotizacion'])->name('pedido.pdf');

    //CATÁLOGOS (Artículos, Clientes)
    Route::prefix('Catalogos')->group(function () {
        // Artículos
        Route::get('/Articulos', [ArticuloController::class, 'index'])->name('articulos');
        Route::post('/Articulo/Estado', [ArticuloController::class, 'activo_inactivo'])->name('estado.Articulo');

        // Clientes
        Route::get('/Clientes', [ClienteController::class, 'index'])->name('clientes');
        Route::post('/Clientes/Estado', [ClienteController::class, 'activo_inactivo'])->name('estado.Cliente');
    });
/*****************************************************************************************************************************************************/
/*****************************************************************************************************************************************************/
/*****************************************************************************************************************************************************/
/*****************************************************************************************************************************************************/
    //ADMINISTRACIÓN (Roles 1 y 2)
    Route::middleware(['role:1,2'])->group(function () {

        /*---------------------- USUARIOS ----------------------*/
        Route::get('/Usuarios', [UsuarioController::class, 'index'])->name('usuarios');
        Route::post('/usuarios/estado', [UsuarioController::class, 'activo_inactivo'])->name('estado.Usuario');

        /*---------------------- ADMIN (prefijo /admin) ----------------------*/
        Route::prefix('admin')->group(function () {
            // Vendedores
            Route::get('/catalogo-vendedores', [VendedorController::class, 'index'])->name('admin.catalogo.vendedores');
            Route::post('/vendedores/toggle-estado', [VendedorController::class, 'toggleActivo'])->name('admin.vendedores.toggleActivo');

            // Marcas
            Route::get('/marcas', [MarcaController::class, 'index'])->name('admin.marcas.index');

            // Usuarios (CRUD)
            Route::get('/usuarios', [UsuarioController::class, 'index'])->name('admin.usuarios.index');
            Route::post('/usuarios', [UsuarioController::class, 'store'])->name('admin.usuarios.store');

            // Consultas AJAX
            Route::get('/ocrd/{cardCode}', [UsuarioController::class, 'getCliente'])->name('admin.ocrd.show');
            Route::get('/oslp/{slpCode}', [UsuarioController::class, 'show'])->name('admin.oslp.show');
            route::view('/sincronizadores', 'admin.SincronizadoresManuales')->name('sincronizadores');
        });

        /*---------------------- CONFIGURACIÓN ----------------------*/
        Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion');
        Route::put('/configuracion', [ConfiguracionController::class, 'update'])->name('GuardarConfig');

        /*---------------------- UTILIDADES  (SOLO DESARROLLO) ----------------------*/ /*************************************************************************/
        Route::post('/monedas', [ App\Http\Controllers\SincronizacionController::class, 'insertarMonedas'])->name('monedas'); //POSIBLEMETE BORRAR
        Route::post('/Articulos', [ App\Http\Controllers\SincronizacionController::class, 'Articulos'])->name('articulosWeb');
    });

    // USUARIOS NORMALES Y VENDEDORES (Roles 3 y 4)
    Route::middleware(['role:3,4'])->group(function () {
       
    });
});
require __DIR__ . '/auth.php';