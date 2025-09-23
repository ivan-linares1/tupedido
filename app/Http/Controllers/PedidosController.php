<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\Clientes;
use App\Models\Cotizacion;
use App\Models\DireccionesClientes;
use App\Models\LineasCotizacion;
use App\Models\Moneda;
use App\Models\Vendedores; 
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PedidosController extends Controller
{
    public function NuevoPedido ($DocEntry = null)
    {
        $IVA = 16;
        $hoy = Carbon::today()->format('Y-m-d'); // Obtiene la fecha de hoy
        $clientes = Clientes::with('descuentos.detalles.marca')->get();
        $vendedores = null;
        $hoy = Carbon::today()->format('Y-m-d');
        $mañana = Carbon::tomorrow()->format('Y-m-d');
        // Fechas por defecto (HOY para nueva cotización)
        $fechaCreacion = $hoy;
        $fechaEntrega  = $mañana;

        
        $user = Auth::user();
        if($user->rol_id == 1 || $user->rol_id == 2)
            $vendedores = Vendedores::all();
        

        $monedas = Moneda::with(['cambios' => function($query) use ($hoy) {
            $query->whereDate('RateDate', $hoy);
        }])->get();

        $articulos = Articulo::with(['precio.moneda.cambios' => function($query) use ($hoy) {
            $query->whereDate('RateDate', $hoy);
        }])->where('Active', 'Y')->get();        
       
        $modo = 0;

        // Valores por defecto
        $preseleccionados = [
            'cliente' => null,
            'vendedor' => null,
            'moneda' => null,
        ];
        
        $lineasComoArticulos = [];

        if ($DocEntry) {
            // Si hay cotización, precargamos datos
            $cotizacion = Cotizacion::with('lineas')->findOrFail($DocEntry);

            $preseleccionados = [
                'cliente' => $cotizacion->CardCode,
                'vendedor' => $cotizacion->SlpCode,
                'moneda' => $cotizacion->DocCur,
            ];

             foreach ($cotizacion->lineas as $linea) {
        // Buscar el artículo ya cargado en $articulos
        $articulo = $articulos->firstWhere('ItemCode', $linea->ItemCode);

        if ($articulo) {
                    $lineasComoArticulos[] = [
                        'ItemCode'   => $articulo->ItemCode,
                        'FrgnName'   => $articulo->FrgnName,
                        'Id_imagen'  => $articulo->Id_imagen,
                        'imagen'     => [
                            'Ruta_imagen' => $articulo->imagen?->Ruta_imagen ?? ''
                        ],
                        'precio' => [
                            'Price'  => $articulo->Precio->Price,
                            'moneda' => $monedas->firstWhere('Currency_ID', $articulo->Precio->Currency_ID)
                        ],
                        'ItmsGrpCod' => $articulo->ItmsGrpCod,
                        'IVA'        => $IVA,
                        'Quantity'   => $linea->Quantity,
                        'DiscPrcnt'  => $linea->DiscPrcnt
                    ];
                }
            }
        }

        //dd($lineasComoArticulos);
        return view('users.pedido', compact('clientes', 'vendedores', 'monedas', 'articulos', 'IVA', 'preseleccionados', 'modo', 'fechaCreacion', 'fechaEntrega', 'lineasComoArticulos'));
    }

    public function ObtenerDirecciones($CardCode){
        // Obtener todas las direcciones de un cliente
        $direcciones = DireccionesClientes::where('CardCode', $CardCode)->get();
        $fiscal = "S/D";
        $entrega = "S/D";

        foreach ($direcciones as $direccion) {
            switch($direccion->Address) {
                case 'FISCAL':
                    if ($direccion->cliente) {
                        $fiscal = $direccion->cliente->CardName . "\n" .
                                ($direccion->Street ?? '') . "\n" .
                                ($direccion->Block ?? '') . "\n" .
                                "C.P. " . ($direccion->ZipCode ?? '') . "\n" .
                                ($direccion->City ?? '') . ", " . ($direccion->County ?? '') . "\n" .
                                ($direccion->State ?? '') . ", " . ($direccion->Country ?? '');
                    }
                    break;

                case 'ENVIO':
                case 'ENVÍO':
                    $entrega = ($direccion->Street ?? '') . "\n" .
                               ($direccion->Block ?? '') . "\n" .
                               "C.P. " . ($direccion->ZipCode ?? '') . "\n" .
                               ($direccion->City ?? '') . ", " . ($direccion->County ?? '') . "\n" .
                               ($direccion->State ?? '') . ", " . ($direccion->Country ?? '');
                    break;
            }
        }

         return response()->json([
            'fiscal' =>$fiscal,
            'entrega' => $entrega,
        ]);
    }

    public function detalles($DocEntry)
    {
        // Obtener la cotización con sus líneas
        $cotizacion = Cotizacion::with('lineas')->findOrFail($DocEntry);

        // Datos adicionales para la vista
        $IVA = 16;
        $hoy = Carbon::today()->format('Y-m-d');

        // Clientes y vendedores
        $clientes = Clientes::with('descuentos.detalles.marca')->get();
        $user = Auth::user();
        $vendedores = ($user->rol_id == 1 || $user->rol_id == 2) ? Vendedores::all() : [];

        // Monedas
        $monedas = Moneda::with(['cambios' => function($query) use ($hoy) {
            $query->whereDate('RateDate', $hoy);
        }])->get();

        $articulos = [];

        // Fechas de la cotización
        $fechaCreacion = $cotizacion->DocDate;
        $fechaEntrega  = $cotizacion->DocDueDate;

        // Modo: 1 = solo ver (todos los campos readonly/disabled)
        $modo = 1;

        // Datos preseleccionados para evitar errores en la vista
        $preseleccionados = [
            'cliente' => $cotizacion->CardCode,
            'vendedor' => $cotizacion->SlpCode,
            'moneda' => $cotizacion->DocCur,
        ];

        // Retornar la vista
        return view('users.cotizacion', compact('cotizacion', 'IVA', 'clientes', 'vendedores', 'monedas', 'articulos', 'modo', 'fechaCreacion', 'fechaEntrega', 'preseleccionados' ));
    }
}
