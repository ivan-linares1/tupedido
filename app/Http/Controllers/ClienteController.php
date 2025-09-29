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

        // Filtro búsqueda
        if ($request->buscar) {
            $cliente->where(function ($q) use ($request) {
                $q->where('CardCode', 'like', "%{$request->buscar}%")
                  ->orWhere('CardName', 'like', "%{$request->buscar}%");
            });
        }

        // Mostrar X registros (paginación)
        $mostrar = $request->mostrar ?? 25;//el 25 esta por default antes de que se seleccione un otro numero de paginacion
        $clientes = $cliente->paginate($mostrar);

        return view('admin.clientesCatalogo', compact('clientes'));
    }
}
