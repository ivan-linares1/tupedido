<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class MarcaController extends Controller
{
    // Método para mostrar todas las marcas
    public function index()
    {
        // Consultamos los registros de la tabla OITB
        $marcas = DB::table('oitb')->get();

        // Enviamos los datos a la vista
        return view('admin.catalogo_marcas', compact('marcas'));
    }
}
