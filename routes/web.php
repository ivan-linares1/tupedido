<?php

use App\Http\Controllers\ArticuloController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\configuracionController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CotizacionesController;
use App\Http\Controllers\PedidosController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VendedorController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\OslpController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('auth')->group(function () {

    //rutasGenerales ambos usuarios
    Route::get('/Dashboard', function () { return view('admin.dashboard'); })->name('dashboard');

    //cotizaciones
    Route::get('/NuevaCotizacion/{DocEntry?}', [CotizacionesController::class, 'NuevaCotizacion'])->name('NuevaCotizacion');
    Route::get('/Cotizaciones', [CotizacionesController::class, 'index'])->name('cotizaciones');
    Route::get('/cliente/{cardCode}/direcciones', [CotizacionesController::class, 'ObtenerDirecciones'])->name('ObtenerDirecciones');
    Route::post('/CotizacionesGuardar', [CotizacionesController::class, 'GuardarCotizacion'])->name('cotizacionSave');
    Route::get('/cotizacion/{id}', [CotizacionesController::class, 'detalles'])->name('detalles');
    Route::get('/cotizacion/pdf/{id}', [CotizacionesController::class, 'pdfCotizacion'])->name('cotizacion.pdf');

    //Pedidos
    Route::get('/NuevPedido/{DocEntry?}', [PedidosController::class, 'NuevoPedido'])->name('NuevaPedido');
    Route::get('/Pedidos', [PedidosController::class, 'index'])->name('Pedidos');
    Route::post('/CotizacionesGuardarPedido', [PedidosController::class, 'GuardarCotizacion'])->name('cotizacionSavePedido');
    Route::get('/Pedido/{id}', [PedidosController::class, 'detallesPedido'])->name('detallesP');
    Route::get('/Pedido/pdf/{id}', [PedidosController::class, 'pdfCotizacion'])->name('pedido.pdf');

    //Articulos
    Route::get('/CatalogosArticulos', [ArticuloController::class, 'index'])->name('articulos'); 
    Route::post('/Articulo/Estado', [ArticuloController::class, 'activo_inactivo'])->name('estado.Articulo');


    // RUTAS PARA EL ADMIN
    Route::middleware(['auth', 'role:1,2'])->group(function () {

        //Usuarios
        Route::get('/Usuarios', [UsuarioController::class, 'index'])->name('usuarios');

         Route::post('/usuarios/estado', [UsuarioController::class, 'activo_inactivo'])->name('estado.Usuario');

        //Clientes
        Route::get('/CatalogosClientes', [ClienteController::class, 'index'])->name('clientes'); 
        Route::post('/Clientes/Estado', [ClienteController::class, 'activo_inactivo'])->name('estado.Cliente'); 


        //Vendedores
        Route::post('/admin/vendedores/toggle-estado', [VendedorController::class, 'toggleActivo'])
            ->name('admin.vendedores.toggleActivo');
        

        // Marcas
       Route::get('/admin/marcas', [MarcaController::class, 'index'])->name('admin.marcas.index');



        //configuracion 
        Route::get('/configuracion', [configuracionController::class, 'index'])->name('configuracion');
        Route::put('/configuracion', [configuracionController::class, 'update'])->name('GuardarConfig');


        // Borrar cuando este en producción *****************
        Route::get('/insertar-monedas', [UsuarioController::class, 'insertarMonedas'])->name('insertar.monedas');

        //Ruta Catálogo de Vendedores (solo admin)
        Route::get('/admin/catalogo-vendedores', [VendedorController::class, 'index'])->name('admin.catalogo.vendedores');
    });

    // RUTAS PARA USUARIOS NORMALES Y VENDEDORES
    Route::middleware(['auth', 'role:3,4'])->group(function () { 
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
