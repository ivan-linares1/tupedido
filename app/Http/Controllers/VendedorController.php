<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vendedores; // Este modelo debes crearlo para apuntar a la tabla oslp

class VendedorController extends Controller
{
    public function index(Request $request)
    {
        $vendedores = Vendedores::query();

        // Filtro estatus
        if ($request->estatus == 'Activos') {
            $vendedores->where('Active', 'Y'); 
        } elseif ($request->estatus == 'Inactivos') {
            $vendedores->where('Active', 'N');
        }

        // Filtro búsqueda
        if ($request->buscar) {
            $vendedores->where(function ($q) use ($request) {
                $q->where('SlpCode', 'like', "%{$request->buscar}%")
                  ->orWhere('SlpName', 'like', "%{$request->buscar}%");
            });
        }

        // Mostrar X registros (paginación)
        $mostrar = $request->mostrar ?? 25; // 25 por default
        $vendedores = $vendedores->paginate($mostrar);

        return view('admin.catalogo_vendedores', compact('vendedores'));
    }
}
