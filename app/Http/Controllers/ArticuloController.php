<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\Marcas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticuloController extends Controller
{
    public function index (Request $request)
    {
        $articulo = Articulo::query();
        $marcas = Marcas::orderBy('ItmsGrpNam', 'asc')->get();

        $user = Auth::user();

        // Filtro estatus ny tambien filtra cuando los activos cuando sea un cliente o un vendedor
        if ($request->estatus == 'Activos' || in_array($user->rol_id, [3, 4])) {
            $articulo->where('Active', 'Y'); 
        } elseif ($request->estatus == 'Inactivos') {
            $articulo->where('Active', 'N');
        }elseif($request->estatus == 'Todos'){ /*SIN FILTRO MUESTRA TODOS*/  }


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

    public function stock (Request $request)
    {
        $errores = [];
        $articulos = $request->input('articulos', []);

        foreach($articulos as $articulo)
        {
            $art = Articulo::where('ItemCode', $articulo['itemCode'])->first();
            if( $articulo['cantidad'] > $art->OnHand)
            {
                $errores[] = [
                    'itemCode' => $articulo['itemCode'],
                    'descripcion' => $articulo['descripcion'],
                    'modelo' => $art->ItemName,
                ];
            }
        }
        
        if(count($errores)>0)
        {
            $mensaje = "Los siguientes artículos exceden el stock disponible:<br>";
            foreach ($errores as $err) {
                $mensaje .= "<li><b>{$err['itemCode']}</b> - {$err['descripcion']} - {$err['modelo']}<br></li>";
            }

            return response()->json([
                'success' => false,
                'mensaje' => $mensaje,
                'errores' => $errores
            ]);
        }

        return response()->json([ 'success' => true, ]);
    }

    public function vistaStock() //funcion que solo hace el cambio de vista puede tambien hacerce directamente en el menu pero por el middleaware decidi dejarlo asi
    {
        return view('admin.consultaStock');
    }

    public function buscarArticulos(Request $request)
    {
        $search = $request->input('search');
        $page = $request->input('page', 1);
        $cantidad = 10;

        $query = Articulo::select('ItemCode', 'ItemName', 'FrgnName')
            ->where('Active', 'Y');

        if ($search) {
            $query->where(function($q) use ($search){
                $q->where('ItemCode', 'LIKE', "%$search%")
                ->orWhere('ItemName', 'LIKE', "%$search%")
                ->orWhere('FrgnName', 'LIKE', "%$search%");
            });
        }

        $articulos = $query->paginate($cantidad, ['*'], 'page', $page);

        // Formato que Select2 requiere
        $results = [];
        foreach ($articulos as $a) {
            $results[] = [
                "id"   => $a->ItemCode,
                "text" => "{$a->ItemCode} - {$a->FrgnName} - {$a->ItemName}"
            ];
        }

        return response()->json([
            "results" => $results,
            "pagination" => [
                "more" => $articulos->hasMorePages()
            ]
        ]);
    }

    public function verStock(Request $request)
    {
        $articulo = Articulo::where('ItemCode', $request->itemCode)->first();

        if (!$articulo) {
            return response()->json(['error' => 'Artículo no encontrado'], 404);
        }

        // Stock disponible
        $stock = $articulo->OnHand;
        $cantidad = $request->cantidad;

        if ($stock >= $cantidad) {
            return response()->json([
                'success' => 'Suficiente stock'
            ]);
        } else {
            return response()->json([
                'error' => 'Stock insuficiente'
            ]);
        }
    }

    
}
