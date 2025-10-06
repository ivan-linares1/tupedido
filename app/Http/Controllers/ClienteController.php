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


        // Mostrar X registros (paginación)
        $mostrar = $request->mostrar ?? 25;//el 25 esta por default antes de que se seleccione un otro numero de paginacion
        $clientes = $cliente->paginate($mostrar);

        return view('admin.clientesCatalogo', compact('clientes'));
    }

    public function activo_inactivo(Request $request)
    {
        $cliente = Clientes::findOrFail($request->id);
        $cliente->{$request->field} = $request->value; //{$request->field es el nombre del campo a acualizar que se guarda como data en el html
        $cliente->save();

        return response()->json(['success' => true]);
    }
}
