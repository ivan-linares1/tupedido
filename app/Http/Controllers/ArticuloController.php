<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\Marcas;
use Illuminate\Http\Request;

class ArticuloController extends Controller
{
    public function index (Request $request)
    {
        $articulo = Articulo::query();
        $marcas = Marcas::orderBy('ItmsGrpNam', 'asc')->get();


        // Filtro estatus
        if ($request->estatus == 'Activos') {
            $articulo->where('Active', 'Y'); 
        } elseif ($request->estatus == 'Inactivos') {
            $articulo->where('Active', 'N');
        }


        //filtro de marca
        if ($request->grupo) {
            $articulo->where('ItmsGrpCod', $request->grupo);
        }

        // Filtro de búsqueda
        if ($request->buscar) {
            $articulo->where(function($q) use ($request) {
                $q->where('ItemCode', 'like', "%{$request->buscar}%")
                ->orWhere('ItemName', 'like', "%{$request->buscar}%")
                ->orWhere('FrgnName', 'like', "%{$request->buscar}%")
                ->orWhereHas('marca', function($query) use ($request) {
                    $query->where('ItmsGrpNam', 'like', "%{$request->buscar}%");
                });
            });
        }

        // Mostrar X registros (paginación)
        $mostrar = $request->mostrar ?? 25;//el 25 esta por default antes de que se seleccione un otro numero de paginacion
        $articulos = $articulo->paginate($mostrar);

        // Si es AJAX, devolvemos solo la tabla
        if ($request->ajax()) {
            return view('partials.tabla_articulos', compact('articulos'))->render();
        }

        return view('users.catalogo_productos', compact('articulos', 'marcas'));
    }

    public function activo_inactivo(Request $request)
    {
        $articulo = Articulo::findOrFail($request->id);
        $articulo->{$request->field} = $request->value; //{$request->field es el nombre del campo a acualizar que se guarda como data en el html
        $articulo->save();

        return response()->json(['success' => true]);
    }
}
