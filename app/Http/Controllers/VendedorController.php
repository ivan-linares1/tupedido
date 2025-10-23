<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vendedores; // Este modelo debes crearlo para apuntar a la tabla oslp

class VendedorController extends Controller
{
    public function index(Request $request)
    {
        $vendedor = Vendedores::query();

        // Filtro estatus
        if ($request->estatus && $request->estatus !== 'Todos') {
            $vendedor->where('Active', $request->estatus);
        }

        // Filtro búsqueda
        if ($request->buscar) {
            $vendedor->where(function ($q) use ($request) {
                $q->where('SlpCode', 'like', "%{$request->buscar}%")
                  ->orWhere('SlpName', 'like', "%{$request->buscar}%");
            });
        }

        // Mostrar X registros (paginación)
        $mostrar = $request->mostrar ?? 25;//el 25 esta por default antes de que se seleccione un otro numero de paginacion
        $vendedores = $vendedor->paginate($mostrar);

        // Si es AJAX, devolvemos solo la tabla
        if ($request->ajax()) {
            return view('partials.tabla_vendedores', compact('vendedores'))->render();
        }

        return view('admin.catalogo_vendedores', compact('vendedores'));
    }
    
    public function toggleActivo(Request $request)
    {
        $vendedor = Vendedores::where('SlpCode', $request->id)->first();

        if ($vendedor) {
            // Cambiar valor Y ↔ N
            $vendedor->Active = $vendedor->Active === 'Y' ? 'N' : 'Y';
            $vendedor->save();

            return response()->json([
                'success' => true,
                'estado' => $vendedor->Active
            ]);
        }

        return response()->json(['success' => false], 404);
    }

}
