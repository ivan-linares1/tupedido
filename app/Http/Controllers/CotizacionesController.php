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


class CotizacionesController extends Controller
{

    public function index(){//se encarga de listar todas las cotizaciones existentes
        // Obtenemos cotizaciones con su vendedor y el nombre de la moneda 
        $cotizaciones = Cotizacion::select( 
                'OQUT.*',
                'OSLP.SlpName as vendedor_nombre',
                'OCRN.Currency as moneda_nombre'
            )
            ->leftJoin('OSLP', 'OQUT.SlpCode', '=', 'OSLP.SlpCode')  // Relación con vendedor
            ->leftJoin('OCRN', 'OQUT.DocCur', '=', 'OCRN.Currency_ID') // Relación con moneda
            ->get();

        return view('admin.cotizaciones', compact('cotizaciones'));
    }

    public function NuevaCotizacion ()
    {
        $IVA = 16;
        $hoy = Carbon::today()->format('Y-m-d'); // Obtiene la fecha de hoy
        $clientes = Clientes::with('descuentos.detalles.marca')->get();
        $vendedores = null;
        
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

        return view('admin.cotizacion', compact('clientes', 'monedas', 'articulos', 'IVA', 'vendedores', 'modo'));
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


    public function GuardarCotizacion(Request $request)
    {
        try {
            // Limpiar valores numéricos
            $totalSinPromo = floatval(str_replace(['$', 'MXM', ','], '', $request->TotalSinPromo));
            $descuento     = floatval(str_replace(['$', 'MXM', ','], '', $request->Descuento));
            $subtotal      = floatval(str_replace(['$', 'MXM', ','], '', $request->Subtotal));
            $iva           = floatval(str_replace(['$', 'MXM', ','], '', $request->iva));
            $total         = floatval(str_replace(['$', 'MXM', ','], '', $request->total));

            // Guardar líneas de cotización
            $articulos = json_decode($request->articulos, true);
            if (is_array($articulos) && count($articulos) < 1){
                return redirect()->route('cotizaciones')->with('error', 'Ocurrio un error no puedes guardar cotizaciones sin articulos');
            }
            

            // Guardar cotización
            $cotizacion = Cotizacion::create([
                'CardCode'      => $request->cliente,
                'DocDate'       => $request->fechaCreacion,
                'DocDueDate'    => $request->fechaEntrega,
                'CardName'      => $request->CardName,
                'SlpCode'       => $request->SlpCode,
                'Phone1'        => $request->phone1,
                'E_Mail'        => $request->email,
                'DocCur'        => $request->DocCur,
                'ShipToCode'    => $request->ShipToCode ?? '',
                'PayToCode'     => $request->PayToCode ?? '',
                'Address'       => $request->direccionFiscal,
                'Address2'      => $request->direccionEntrega,
                'TotalSinPromo' => $totalSinPromo,
                'Descuento'     => $descuento,
                'Subtotal'      => $subtotal,
                'IVA'           => $iva,
                'Total'         => $total,
            ]);


            $lineNum = 0;

            foreach ($articulos as $art) {
                $lineNum++;
                LineasCotizacion::create([
                    'DocEntry'   => $cotizacion->DocEntry,
                    'LineNum'    => $lineNum,
                    'ItemCode'   => $art['itemCode'],
                    'U_Dscr'     => $art['descripcion'],
                    'unitMsr2'   => $art['unidad'],
                    'Price'      => floatval(str_replace(',', '', $art['precio'])),
                    'DiscPrcnt'  => floatval(str_replace(['%', ','], '', $art['descuentoPorcentaje'])),
                    'Quantity'   => floatval($art['cantidad']),
                    'Id_imagen'  => $art['imagen'] ?? null,
                ]);
            }

            return redirect()->route('cotizaciones')
                            ->with('success', 'Cotización guardada correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('cotizaciones')
                            ->with('error', 'Ocurrió un error al guardar la cotización: ' . $e->getMessage());
        }
    }

   public function detalles($DocEntry)
    {
        // Obtener la cotización con sus líneas
        $cotizacion = Cotizacion::with('lineas')->findOrFail($DocEntry);

        // Datos adicionales para la vista
        $IVA = 16;
        $hoy = Carbon::today()->format('Y-m-d');

        // Clientes
        $clientes = Clientes::with('descuentos.detalles.marca')->get();

        // Vendedores
        $user = Auth::user();
        $vendedores = null;
        if($user->rol_id == 1 || $user->rol_id == 2){
            $vendedores = Vendedores::all();
        }

        // Monedas con el tipo de cambio del día
        $monedas = Moneda::with(['cambios' => function($query) use ($hoy) {
            $query->whereDate('RateDate', $hoy);
        }])->get();

        $articulos = Articulo::with(['precio.moneda.cambios' => function($query) use ($hoy) {
            $query->whereDate('RateDate', $hoy);
        }])->where('Active', 'Y')->get(); 

        // Modo: 1 = solo ver (todos los campos readonly/disabled)
        $modo = 1;

        // Retornar la misma vista de cotización
        return view('admin.cotizacion', compact('cotizacion', 'IVA', 'clientes', 'vendedores', 'monedas', 'articulos', 'modo' ));
    }

}
