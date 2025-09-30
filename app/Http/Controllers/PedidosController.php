<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\Clientes;
use App\Models\Cotizacion;
use App\Models\configuracion;
use App\Models\DireccionesClientes;
use App\Models\LineasCotizacion;
use App\Models\Moneda;
use App\Models\pedidos;
use App\Models\Vendedores; 
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PedidosController extends Controller
{
    
    public function index()
    {
        // Obtener todos los pedidos con la cotización asociada
        $pedidos = pedidos::with(['cotizacionBase'])->orderBy('DocEntry', 'desc')->get();

        // Transformar para la vista
        $pedidosList = $pedidos->map(function($pedido) {
            $cotizacion = $pedido->cotizacionBase;

            return (object)[
                'DocEntry' => $pedido->DocEntry,
                'CotizacionDocEntry' => $cotizacion->DocEntry ?? null,
                'CotizacionFecha' => $cotizacion->DocDate ?? null,
                'Cliente' => $cotizacion->CardName ?? null,
                'Vendedor' => $cotizacion->vendedor->SlpName, 
                'Total' => $cotizacion->Total ?? 0,
                'Moneda' => $cotizacion->moneda_nombre ?? 'MXN',
            ];
        });

        return view('users.pedidos', ['pedidos' => $pedidosList]);
    }

    public function NuevoPedido ($DocEntry = null)
    {
        $IVA = configuracion::firstOrFail()->iva;
        $hoy = Carbon::today()->format('Y-m-d');
        $mañana = Carbon::tomorrow()->format('Y-m-d');

        // Fechas por defecto (HOY para nueva cotización)
        $fechaCreacion = $hoy;
        $fechaEntrega  = $mañana;

        $clientes = Clientes::with('descuentos.detalles.marca')->get();

        $user = Auth::user();
        $vendedores = ($user->rol_id == 1 || $user->rol_id == 2)
            ? Vendedores::all()
            : null;

        // Monedas con cambios del día
        $monedas = Moneda::with(['cambios' => function($query) use ($hoy) {
            $query->whereDate('RateDate', $hoy);
        }])->get();

        // Artículos activos que tengan cambios en su moneda para hoy
        $articulos = Articulo::where('Active', 'Y')
            ->whereHas('precio.moneda.cambios', function($query) use ($hoy) {
                $query->whereDate('RateDate', $hoy);
            })
            ->with(['precio.moneda.cambios' => function($query) use ($hoy) {
                $query->whereDate('RateDate', $hoy);
            }, 'imagen'])
            ->get();

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
                'cliente'  => $cotizacion->CardCode,
                'vendedor' => $cotizacion->SlpCode,
                'moneda'   => $cotizacion->DocCur,
            ];

            foreach ($cotizacion->lineas as $linea) {
                $articulo = $articulos->firstWhere('ItemCode', $linea->ItemCode);

                if ($articulo) {
                    // Clonamos el objeto artículo y agregamos los datos de la cotización
                    $artClone = clone $articulo;
                    $artClone->Quantity  = $linea->Quantity;

                    $lineasComoArticulos[] = $artClone;
                }
            }
        }

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

    public function detallesPedido($DocEntry)
    {
        // Obtener la cotización con sus líneas
        $cotizacion = Cotizacion::with('lineas')->findOrFail($DocEntry);
        $idPedido = pedidos::where('BaseEntry', $DocEntry)->first()?->DocEntry;

        // Datos adicionales para la vista
        $IVA = configuracion::firstOrFail()->iva;
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
        return view('users.Pedido', compact('cotizacion', 'IVA', 'clientes', 'vendedores', 'monedas', 'articulos', 'modo', 'fechaCreacion', 'fechaEntrega', 'preseleccionados', 'idPedido' ));
    }


    public function GuardarCotizacion(Request $request)
    {
        try {
            //Validaciones
            $request->validate([
                'cliente'          => 'required',
                'fechaCreacion'    => 'required',
                'fechaEntrega'     => 'required',
                'CardName'         => 'required',
                'SlpCode'          => 'required',
                'phone1'           => 'required',
                'email'            => 'required',
                'DocCur'           => 'required',
                //'ShipToCode'       => 'required',
                //'PayToCode'        => 'required',
                'direccionFiscal'  => 'required',
                'direccionEntrega' => 'required',
                'TotalSinPromo'    => 'required',
                'Descuento'        => 'required',
                'Subtotal'         => 'required',
                'iva'              => 'required',
                'total'            => 'required',
                'articulos'        => 'required|json',
            ], [
                // Mensajes personalizados
                'required' => 'El campo :attribute es obligatorio.',
                'json'     => 'Los artículos deben enviarse en formato JSON válido.',
            ], [
                //quivalencias de atributos
                'CardName'         => 'Nombre del cliente',
                'SlpCode'          => 'Vendedor',
                'phone1'           => 'Teléfono',
                'email'            => 'Correo electrónico',
                'DocCur'           => 'Moneda',
                //'ShipToCode'       => 'Dirección de envío',
                //'PayToCode'        => 'Dirección de pago',
            ]);

            // Guardar líneas de cotización
            $articulos = json_decode($request->articulos, true);
            if (is_array($articulos) && count($articulos) < 1){
                return back()->with('error', 'Ocurrio un error no puedes guardar cotizaciones sin articulos');
            }

            // Limpiar valores numéricos
            $totalSinPromo = floatval(str_replace(['$', 'MXM', ','], '', $request->TotalSinPromo));
            $descuento     = floatval(str_replace(['$', 'MXM', ','], '', $request->Descuento));
            $subtotal      = floatval(str_replace(['$', 'MXM', ','], '', $request->Subtotal));
            $iva           = floatval(str_replace(['$', 'MXM', ','], '', $request->iva));
            $total         = floatval(str_replace(['$', 'MXM', ','], '', $request->total));

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

            $this->guardarPedido($cotizacion->DocEntry);
            return $this->detallesPedido($cotizacion->DocEntry);

        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error al guardar la cotización: ' . $e->getMessage());
        }
    }

    public function guardarPedido($DocEntry){
        $Pedido = pedidos::create([
            'fecha' => Carbon::today()->format('Y-m-d'),
            'LineNum' => null,
            'TargetType' => null,
            'TrgetEntry' => null,
            'BaseRef' => $DocEntry,
            'BaseEntry' => $DocEntry,
        ]);

         return $Pedido->DocEntry;
    }
}
