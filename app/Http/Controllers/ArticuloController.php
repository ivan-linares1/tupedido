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
        $marcas = Marcas::all();


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

        // Mostrar X registros (paginación)
        $mostrar = $request->mostrar ?? 25;//el 25 esta por default antes de que se seleccione un otro numero de paginacion
        $articulos = $articulo->paginate($mostrar);

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
