<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\Cotizacion;
use App\Models\configuracion;
use App\Models\DireccionesClientes;
use App\Models\LineasPedidos;
use App\Models\Moneda;
use App\Models\Pedido;
use App\Models\Vendedores; 
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PedidosController extends Controller
{
    
    public function index(Request $request)
    {
        $user = Auth::user();
        $configuracionVacia = Configuracion::count() === 0;

        $buscar = $request->input('buscar');
        $fecha = $request->input('fecha');
        $mostrar = $request->input('mostrar', 10);

        $query = Pedido::with(['vendedor', 'moneda'])
            ->orderBy('DocEntry', 'desc');

        // Filtrado por rol
        if (in_array($user->rol_id, [1, 2])) {
            // Admin / SuperAdmin: ven todo
        } elseif ($user->rol_id == 3) {
            // Cliente
            $query->where('CardCode', $user->codigo_cliente);
        } elseif ($user->rol_id == 4) {
            // Vendedor
            $query->where('SlpCode', $user->codigo_vendedor);
        } else {
            abort(403, 'Rol no permitido');
        }

        // Filtro de búsqueda
        if ($buscar) {
            $query->where(function ($q) use ($buscar) {
                $q->where('DocEntry', 'like', "%$buscar%")
                ->orWhere('CardName', 'like', "%$buscar%")
                ->orWhereHas('vendedor', function ($sub) use ($buscar) {
                    $sub->where('SlpName', 'like', "%$buscar%");
                });
            });
        }

        //Filtro por fecha
        if ($fecha) {
            $query->whereDate('DocDate', $fecha);
        }

        //Paginación
        $pedidos = $query->paginate($mostrar)->withQueryString();

        //Si es AJAX, devolver solo la tabla
        if ($request->ajax()) {
            return view('partials.tabla_pedidos', compact('pedidos'))->render();
        }

        return view('users.pedidos', compact('pedidos', 'configuracionVacia'));
    }

    public function NuevoPedido ($DocEntry = null)
    {
        $IVA = configuracion::firstOrFail()->iva;
        $hoy = Carbon::today()->format('Y-m-d');
        $mañana = Carbon::tomorrow()->format('Y-m-d');
        $cotizacion = null;
        $pedido = null;

        // Fechas por defecto (HOY para nueva cotización)
        $fechaCreacion = $hoy;
        $fechaEntrega  = $mañana;

        #$clientes = Clientes::with('descuentos.detalles.marca')->where('Active', 'Y')->get();
        $clientes = [];

        $user = Auth::user();
        $vendedores = Vendedores::where('Active', 'Y')->get();

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
           'cliente' => ($user->rol_id == 3) ? $user->codigo_cliente : null,
            'vendedor' => ($user->rol_id == 4) ? $user->codigo_vendedor : null,
            'moneda' => configuracion::firstOrFail()->MonedaPrincipal,
            'comentario' =>null,
        ];

        $lineasComoArticulos = [];

        if ($DocEntry) {
            // Si hay cotización, precargamos datos
            $cotizacion = Cotizacion::with('lineas')->findOrFail($DocEntry);

            $preseleccionados = [
                'cliente'  => $cotizacion->CardCode,
                'vendedor' => $cotizacion->SlpCode,
                'moneda'   => $cotizacion->DocCur,
                'comentario' =>$cotizacion->comment,
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

        return view('users.Pedido', compact('clientes', 'vendedores', 'monedas', 'articulos', 'IVA', 'preseleccionados', 'modo', 'fechaCreacion', 'fechaEntrega', 'lineasComoArticulos', 'cotizacion', 'pedido'));
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
        $user = Auth::user();

        // Obtener el pedido con sus líneas
        $pedido = Pedido::with(['lineas'])->findOrFail($DocEntry);

        // Validar permisos según rol
        if (in_array($user->rol_id, [1, 2])) {
            // Admin y super admin pueden ver todo
        } elseif ($user->rol_id == 3) {
            // Cliente: solo puede ver sus propios pedidos
            if ($pedido->CardCode != $user->codigo_cliente) {
                abort(403, 'No tienes permiso para ver este pedido.');
            }
        } elseif ($user->rol_id == 4) {
            // Vendedor: solo puede ver sus propios pedidos
            if ($pedido->SlpCode != $user->codigo_vendedor) {
                abort(403, 'No tienes permiso para ver este pedido.');
            }
        } else {
            abort(403, 'Rol no permitido.');
        }

        // Ver si este pedido tiene base en una cotización
        $cotizacion = null;
        $baseEntryLinea = $pedido->lineas->first()?->BaseEntry;
        if ($baseEntryLinea) {
            $cotizacion = Cotizacion::find($baseEntryLinea);
        }

        // Datos adicionales para la vista
        $IVA = configuracion::firstOrFail()->iva;
        $hoy = Carbon::today()->format('Y-m-d');

        // Clientes y vendedores
        //$clientes = Clientes::with('descuentos.detalles.marca')->get();
        $clientes = [];
        $vendedores = Vendedores::where('Active', 'Y')->get();

        // Monedas
        $monedas = Moneda::with(['cambios' => function($query) use ($hoy) {
            $query->whereDate('RateDate', $hoy);
        }])->get();

        $articulos = [];

        // Fechas del pedido
        $fechaCreacion = $pedido->DocDate;
        $fechaEntrega  = $pedido->DocDueDate;

        // Modo: 1 = solo ver (todos los campos readonly/disabled)
        $modo = 1;

        // Datos preseleccionados para evitar errores en la vista
        $preseleccionados = [
            'cliente' => $pedido->CardCode,
            'vendedor' => $pedido->SlpCode,
            'moneda' => $pedido->DocCur,
            'comentario' => $pedido->comment,
        ];

        // Retornar la vista
        return view('users.Pedido', compact('cotizacion', 'IVA', 'clientes', 'vendedores', 'monedas', 'articulos', 'modo', 'fechaCreacion', 'fechaEntrega', 'preseleccionados', 'pedido'));
    }


    public function guardarPedido(Request $request)
    {
        //dd($request->all());
        try {
            //  Validaciones
            $request->validate([
                'cliente'          => 'required',
                'fechaCreacion'    => 'required|date',
                'fechaEntrega'     => 'required|date',
                'CardName'         => 'required',
                'DocCur'           => 'required',
                'direccionFiscal'  => 'required',
                'direccionEntrega' => 'required',
                'TotalSinPromo'    => 'required',
                'Descuento'        => 'required',
                'Subtotal'         => 'required',
                'iva'              => 'required',
                'total'            => 'required',
                'articulos'        => 'required|json',
            ]);

            //  Parsear artículos
            $articulos = json_decode($request->articulos, true);
            if (!is_array($articulos) || count($articulos) < 1) {
                return back()->with('error', 'No se puede guardar un pedido sin artículos.');
            }

            // Limpiar valores numéricos
            $totalSinPromo = floatval(str_replace(['$', 'MXM', ','], '', $request->TotalSinPromo));
            $descuento     = floatval(str_replace(['$', 'MXM', ','], '', $request->Descuento));
            $subtotal      = floatval(str_replace(['$', 'MXM', ','], '', $request->Subtotal));
            $iva           = floatval(str_replace(['$', 'MXM', ','], '', $request->iva));
            $total         = floatval(str_replace(['$', 'MXM', ','], '', $request->total));

            // Crear Pedido (ORDR)
            $pedido = Pedido::create([
                'CardCode'      => $request->cliente,
                'DocDate'       => $request->fechaCreacion,
                'DocDueDate'    => $request->fechaEntrega,
                'CardName'      => $request->CardName,
                'SlpCode'       => $request->SlpCode ?? null,
                'Phone1'        => $request->phone1 ?? '',
                'E_Mail'        => $request->email ?? '',
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
                'comment'       => $request->comentarios ?? null,
            ]);

            if (!$pedido || !$pedido->DocEntry) {
                return back()->with('error', 'No se pudo generar el pedido.');
            }

            // Guardar líneas del pedido (RDR1)
            $lineNum = 0;
            foreach ($articulos as $art) {
                $lineNum++;

                LineasPedidos::create([
                    'DocEntry'   => $pedido->DocEntry,
                    'LineNum'    => $lineNum,
                    'ItemCode'   => $art['itemCode'],
                    'U_Dscr'     => $art['descripcion'] ?? '',
                    'unitMsr2'   => $art['unidad'] ?? '',
                    'Price'      => floatval(str_replace(',', '', $art['precio'] ?? 0)),
                    'DiscPrcnt'  => floatval(str_replace(['%', ','], '', $art['descuentoPorcentaje'] ?? 0)),
                    'Quantity'   => floatval($art['cantidad'] ?? 0),
                    'Id_imagen'  => $art['imagen'] ?? null,
                    'BaseEntry'  => $request->BaseEntry ?? null,
                    'TargetType' => $art['TargetType'] ?? null,
                    'TrgetEntry' => $art['TrgetEntry'] ?? null,
                    'BaseRef'    => $art['BaseRef'] ?? null,
                ]);
            }

            // Retornar detalles del pedido
            return $this->detallesPedido($pedido->DocEntry);

        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error al guardar el pedido: ' . $e->getMessage());
        }
    }


    public function pdfPedido($id)
    {
        // Buscamos el pedido
        $pedido = Pedido::with('lineas')->findOrFail($id);

        // Si tiene cotización base
        $cotizacion = Cotizacion::with('lineas')->find($pedido->BaseEntry);

        $data = [
           'logo' => resource_path('views/pdf/logo.png'),
            'titulo'  => 'PEDIDO',
            'subtitulo'  => 'Pedido',
            'numero'  => $pedido->DocEntry,
            'fecha'   => $pedido->DocDate,
            'vendedor' => $pedido->vendedor->SlpName ?? ($cotizacion->vendedor->SlpName ?? ''),
            'moneda'   => $pedido->moneda->Currency ?? ($cotizacion->moneda->Currency ?? ''),
            'comentario' => $pedido->comment ?? ($cotizacion->comment ?? ''),

            'cliente' => [
                'codigo'     => $pedido->CardCode ?? ($cotizacion->CardCode ?? ''),
                'nombre'     => $pedido->CardName ?? ($cotizacion->CardName ?? ''),
                'dir_fiscal' => $pedido->Address ?? ($cotizacion->Address ?? ''),
                'dir_envio'  => $pedido->Address2 ?? ($cotizacion->Address2 ?? ''),
                'email'      => $pedido->E_Mail ?? ($cotizacion->E_Mail ?? ''),
                'telefono'   => $pedido->Phone1 ?? ($cotizacion->Phone1 ?? ''),
            ],

            'lineas' => array_chunk(
                $pedido->lineas->map(function($l) {
                    return [
                        'codigo'      => $l->ItemCode,
                        'descripcion' => $l->U_Dscr,
                        'cantidad'    => $l->Quantity,
                        'precio'      => $l->Price,
                    ];
                })->toArray(), 25
            ),

            'totales' => [
                'subtotal' => number_format($pedido->Subtotal ?? 0, 2),
                'iva'      => number_format($pedido->IVA ?? 0, 2),
                'total'    => number_format($pedido->Total ?? 0, 2),
            ]
        ];

        $pdf = Pdf::loadView('pdf.documento', $data)->setPaper('letter', 'portrait');

        $pdf->output();
        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->getCanvas();

        // Footer en todas las páginas
        $canvas->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
            $user = Auth::user()->nombre;
            $texto = "Documento generado automáticamente el " . date('d/m/Y H:i') . 
                    " — Página $pageNumber de $pageCount   Autor: $user";
            $font = $fontMetrics->get_font("Calibri", "normal");
            $size = 6;

            $width = $canvas->get_width();
            $textWidth = $fontMetrics->get_text_width($texto, $font, $size);

            $x = ($width - $textWidth) / 2;
            $y = $canvas->get_height() - 20;

            $canvas->text($x, $y, $texto, $font, $size, [0,0,0]);
        });

        return $pdf->stream("Pedido-{$pedido->DocEntry}.pdf");
    }
}