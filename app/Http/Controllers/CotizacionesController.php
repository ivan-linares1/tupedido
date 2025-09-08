<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\Clientes;
use App\Models\DireccionesClientes;
use App\Models\Moneda;
use Illuminate\Support\Facades\Artisan;
use PhpParser\Node\Stmt\Break_;

class CotizacionesController extends Controller
{
    public function index ()
    {
        $clientes = Clientes::all();
        $monedas = Moneda::all();
        $articulos = Articulo::with('precios.moneda.cambios')->where('Active', 'Y')->get();        

        return view('admin.cotizacion', compact('clientes', 'monedas', 'articulos'));
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
}
