<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use Illuminate\Http\Request;

class ArticuloController extends Controller
{
    public function index (Request $request)
    {
        $articulo = Articulo::query();

        // Filtro estatus
        if ($request->estatus == 'Activos') {
            $articulo->where('Active', 'Y'); 
        } elseif ($request->estatus == 'Inactivos') {
            $articulo->where('Active', 'N');
        }

        // Filtro búsqueda
        if ($request->buscar) {
            $articulo->where(function ($q) use ($request) {
                $q->where('ItemCode', 'like', "%{$request->buscar}%")
                  ->orWhere('ItemName', 'like', "%{$request->buscar}%")
                  ->orWhere('FrgnName', 'like', "%{$request->buscar}%");
            });
        }

        // Mostrar X registros (paginación)
        $mostrar = $request->mostrar ?? 25;//el 25 esta por default antes de que se seleccione un otro numero de paginacion
        $articulos = $articulo->paginate($mostrar);

        return view('admin.catalogo_productos', compact('articulos'));
    }
}
