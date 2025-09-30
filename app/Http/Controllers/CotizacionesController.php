<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\Clientes;
use App\Models\Cotizacion;
use App\Models\configuracion;
use App\Models\DireccionesClientes;
use App\Models\LineasCotizacion;
use App\Models\Moneda;
use App\Models\Vendedores; 
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;


class CotizacionesController extends Controller
{

    public function index(){//se encarga de listar todas las cotizaciones existentes
        $user = Auth::user();
        if($user->rol_id == 1 || $user->rol_id == 2){
           return $this->TodasLasCotizaciones();
        }
        else
            return $this->CotizacionesCliente();
    }

    public function CotizacionesCliente()
    {
        $cotizaciones =[];

        return view('users.cotizaciones', compact('cotizaciones'));
    }

    public function TodasLasCotizaciones(){
        // Obtenemos cotizaciones con su vendedor y el nombre de la moneda 
        $cotizaciones = Cotizacion::select( 
                'OQUT.*',
                'OSLP.SlpName as vendedor_nombre',
                'OCRN.Currency as moneda_nombre'
            )
            ->leftJoin('OSLP', 'OQUT.SlpCode', '=', 'OSLP.SlpCode')  // Relación con vendedor
            ->leftJoin('OCRN', 'OQUT.DocCur', '=', 'OCRN.Currency_ID') // Relación con moneda
            ->orderBy('OQUT.DocDate', 'desc')
            ->get();

        return view('users.cotizaciones', compact('cotizaciones'));
    }

    public function NuevaCotizacion ($DocEntry = null)
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

        return view('users.cotizacion', compact('clientes', 'vendedores', 'monedas', 'articulos', 'IVA', 'preseleccionados', 'modo', 'fechaCreacion', 'fechaEntrega', 'lineasComoArticulos'));
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
                    'fecha' => Carbon::today()->format('Y-m-d'),
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

            return redirect()->route('cotizaciones')->with('success', 'Cotización guardada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error al guardar la cotización: ' . $e->getMessage());
        }
    }

    public function detalles($DocEntry)
    {
        // Obtener la cotización con sus líneas
        $cotizacion = Cotizacion::with('lineas')->findOrFail($DocEntry);

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
        return view('users.cotizacion', compact('cotizacion', 'IVA', 'clientes', 'vendedores', 'monedas', 'articulos', 'modo', 'fechaCreacion', 'fechaEntrega', 'preseleccionados' ));
    }


    public function pdfCotizacion($id)
    {
        $cotizacion = Cotizacion::with('lineas')->findOrFail($id);

        $data = [
            'logo'    => public_path('storage/' . configuracion::firstOrFail()->ruta_logo,),
            'titulo'  => 'COTIZACIÓN',
            'subtitulo'  => 'Cotización',
            'numero'  =>  $cotizacion->DocEntry,
            'fecha'   => $cotizacion->DocDate,
            

            'cliente' => [
                'nombre'   => $cotizacion->CardName,
                'email'    => $cotizacion->E_Mail,
                'telefono' => $cotizacion->Phone1,
            ],
            'lineas' => $cotizacion->lineas->map(function($l) {
                return [
                    'codigo'      => $l->ItemCode,
                    'descripcion' => $l->U_Dscr,
                    'cantidad'    => $l->Quantity,
                    'precio'      => $l->Price,
                ];
            })->toArray(),
            'totales' => [
                'subtotal' => $cotizacion->Subtotal,
                'iva'      => $cotizacion->IVA,
                'total'    => $cotizacion->Total,
            ]
        ];

        $pdf = Pdf::loadView('pdf.documento', $data)->setPaper('lette', 'portrait');

        return $pdf->stream("Cotizacion-{$cotizacion->DocEntry}.pdf");
    }

}
