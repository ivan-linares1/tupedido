<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index (Request $request)
    {
        $cliente = Clientes::query();

        // Filtro estatus
        if ($request->estatus == 'Activos') {
            $cliente->where('Active', 'Y'); 
        } elseif ($request->estatus == 'Inactivos') {
            $cliente->where('Active', 'N');
        }
        elseif($request->estatus == 'Todos'){
        }

        // Filtro de búsqueda
        if ($request->buscar) {
            $cliente->where(function($q) use ($request) {
                $q->where('CardCode', 'like', "%{$request->buscar}%")
                ->orWhere('CardName', 'like', "%{$request->buscar}%");
            });
        }


        // Mostrar X registros (paginación)
        $mostrar = $request->mostrar ?? 25;//el 25 esta por default antes de que se seleccione un otro numero de paginacion
        $clientes = $cliente->paginate($mostrar);

        // Si es AJAX, devolvemos solo la tabla
        if ($request->ajax()) {
            return view('partials.tabla_cliente', compact('clientes'))->render();
        }

        return view('admin.clientesCatalogo', compact('clientes'));
    }


    public function buscar(Request $request)
    {
        $search = trim($request->get('q', ''));
        $page = (int) $request->get('page', 1);
        $perPage = 20;

        $query = Clientes::with('descuentos.detalles.marca')
            ->where('Active', 'Y');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('CardName', 'like', "%{$search}%")
                ->orWhere('CardCode', 'like', "%{$search}%");
            });
        }

        $total = $query->count();

        $clientes = $query->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get([
                'CardCode',
                'CardName',
                'phone1',
                'e-mail',
                'GroupNum',
                'Active'
            ]);

        return response()->json([
            'items' => $clientes->map(function ($c) {
                return [
                    'id'         => $c->CardCode,
                    'text'       => "{$c->CardCode} - {$c->CardName}",
                    'cardname'   => $c->CardName,
                    'phone'      => $c->phone1,
                    'email'      => $c->{'e-mail'},
                    'groupNum'   => $c->GroupNum,
                    'active'     => $c->Active,
                    // 🔹 aquí vuelven los descuentos
                    'descuentos' => $c->descuentos
                        ->flatMap(fn($d) => 
                            $d->detalles->map(fn($dd) => [
                                'ObjKey'   => $dd->ObjKey,
                                'Discount' => $dd->Discount,
                                'Marca'    => optional($dd->marca)->Name ?? null
                            ])
                        )
                        ->values()
                        ->toArray(),
                ];
            }),
            'more' => ($page * $perPage) < $total
        ]);
    }

}
