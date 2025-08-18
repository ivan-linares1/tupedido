<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UsuarioController extends Controller
{
    // Muestra la lista de usuarios
    public function index()
    {
        // Obtener todos los usuarios de la base de datos
        $usuarios = User::all();

        // Retornar la vista y pasar los usuarios
        return view('user', compact('usuarios'));
    }
}
