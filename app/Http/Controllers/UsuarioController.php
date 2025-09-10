<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    // Muestra la lista de usuarios
    public function index()
    {
        $usuarios = User::all();

        // Traemos los clientes de OCRD
        $clientes = DB::table('ocrd')->select('CardCode','CardName')->get();

        return view('admin.user', compact('usuarios', 'clientes'));
    }

    // Guarda un nuevo usuario
    public function store(Request $request)
    {
        $request->validate([
            'cliente'   => 'required',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|confirmed|min:6',
        ]);

        // Obtenemos datos del cliente
        $cliente = DB::table('ocrd')->where('CardCode', $request->cliente)->first();

        $user = new User();
        $user->email    = $request->email;
        $user->password = bcrypt($request->password);
        $user->rol_id   = 3; // 👈 Rol por defecto = 3
        $user->activo   = 1;

        // Si existe el cliente, tomamos sus datos
        if ($cliente) {
            $user->nombre = $cliente->CardName;
            // si tu tabla users tiene más campos relacionados, aquí los llenas
        }

        $user->save();

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario creado correctamente');
    }

    // Devuelve info del cliente (para AJAX en el modal)
    public function getCliente(Request $request)
    {
        $cardCode = $request->cardCode;

        // Cliente en OCRD
        $cliente = DB::table('ocrd')
            ->select('CardCode', 'CardName')
            ->where('CardCode', $cardCode)
            ->first();

        if (!$cliente) {
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }

        // Dirección Fiscal (S)
        $direccionFiscal = DB::table('crd1')
            ->where('CardCode', $cardCode)
            ->where('AdresType', 'S')
            ->first();

        // Dirección de Envío (B)
        $direccionEnvio = DB::table('crd1')
            ->where('CardCode', $cardCode)
            ->where('AdresType', 'B')
            ->first();

        // Armamos respuesta
        $data = [
            "CardCode"        => $cliente->CardCode ?? "*SIN DATO*",
            "Nombres"         => $cliente->CardName ?? "*SIN DATO*",
            "ApellidoPaterno" => "*SIN DATO*", // No existe en OCRD
            "ApellidoMaterno" => "*SIN DATO*",
            "Telefono"        => "*SIN DATO*", // Ajusta si tienes Tel1/Cellular
            "TelefonoCelular" => "*SIN DATO*",
            "DireccionFiscal" => $direccionFiscal
                ? trim(($direccionFiscal->Street ?? '') . ', ' . ($direccionFiscal->Block ?? '') . ', ' .
                       ($direccionFiscal->City ?? '') . ', ' . ($direccionFiscal->State ?? '') . ', ' .
                       ($direccionFiscal->ZipCode ?? '') . ', ' . ($direccionFiscal->Country ?? ''), ' ,')
                : "*SIN DATO*",
            "DireccionEnvio" => $direccionEnvio
                ? trim(($direccionEnvio->Street ?? '') . ', ' . ($direccionEnvio->Block ?? '') . ', ' .
                       ($direccionEnvio->City ?? '') . ', ' . ($direccionEnvio->State ?? '') . ', ' .
                       ($direccionEnvio->ZipCode ?? '') . ', ' . ($direccionEnvio->Country ?? ''), ' ,')
                : "*SIN DATO*",
        ];

        return response()->json($data);
    }
}
