<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    // Muestra la lista de usuarios con búsqueda, filtros y paginación
    public function index(Request $request)
    {
        $query = User::with('rol')->orderBy('id', 'desc');

        // Filtro por búsqueda
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('email', 'like', "%{$buscar}%")
                  ->orWhere('nombre', 'like', "%{$buscar}%");
            });
        }

        // Filtro por estatus
        if ($request->estatus === 'Activo') {
            $query->where('activo', 1);
        } elseif ($request->estatus === 'Inactivo') {
            $query->where('activo', 0);
        }

        // Cantidad de registros por página
        $mostrar = $request->mostrar ?? 25;
        $usuarios = $query->paginate($mostrar);

        // Si es AJAX, renderizamos solo la tabla
        if ($request->ajax()) {
            return view('partials.tabla_usuario', compact('usuarios'))->render();
        }

        // Vista completa: también necesitamos clientes y vendedores para los modales
        $clientesConUsuario = User::whereNotNull('codigo_cliente')->pluck('codigo_cliente')->toArray();
        $vendedoresConUsuario = User::whereNotNull('codigo_vendedor')->pluck('codigo_vendedor')->toArray();

        $clientes = DB::table('ocrd')
            ->select('CardCode','CardName')
            ->whereNotIn('CardCode', $clientesConUsuario)
            ->get();

        $vendedores = DB::table('oslp')
            ->select('SlpCode','SlpName','Active')
            ->where('Active','Y')
            ->whereNotIn('SlpCode', $vendedoresConUsuario)
            ->get();

        return view('admin.user', compact('usuarios', 'clientes', 'vendedores'));
    }

    // Guarda un nuevo usuario
    public function store(Request $request)
    {
        // Si viene cliente, es usuario cliente
        if ($request->has('cliente')) {
            $request->validate([
                'cliente'   => 'required',
                'email'     => 'required|email|unique:users,email',
                'password'  => 'required|confirmed|min:6',
            ]);

            if (User::where('codigo_cliente', $request->cliente)->exists()) {
                return redirect()->back()->with('error', 'El cliente ya tiene un usuario creado.');
            }

            $cliente = DB::table('ocrd')->where('CardCode', $request->cliente)->first();

            $user = new User();
            $user->email          = $request->email;
            $user->password       = Hash::make($request->password);
            $user->rol_id         = 3; // Rol cliente
            $user->activo         = 1;
            $user->nombre         = $cliente->CardName ?? 'Cliente';
            $user->codigo_cliente = $cliente->CardCode;

            $user->save();

            return redirect()->route('admin.usuarios.index')->with('success', 'Usuario cliente creado correctamente');
        }

        // Si viene slpcode, es usuario vendedor
        if ($request->has('slpcode')) {
            $request->validate([
                'slpcode'  => 'required',
                'email'    => 'required|email|unique:users,email',
                'password' => 'required|confirmed|min:6',
            ]);

            if (User::where('codigo_vendedor', $request->slpcode)->exists()) {
                return redirect()->back()->with('error', 'El vendedor ya tiene un usuario creado.');
            }

            $vendedor = DB::table('oslp')->where('SlpCode', $request->slpcode)->first();

            $user = new User();
            $user->email           = $request->email;
            $user->password        = Hash::make($request->password);
            $user->rol_id          = 4; // Rol vendedor
            $user->activo          = 1;
            $user->nombre          = $vendedor->SlpName ?? 'Vendedor';
            $user->codigo_vendedor = $vendedor->SlpCode;

            $user->save();

            return redirect()->route('admin.usuarios.index')->with('success', 'Usuario vendedor creado correctamente');
        }

        return redirect()->back()->with('error', 'No se pudo determinar el tipo de usuario a crear.');
    }

    // Toggle activo/inactivo
    public function activo_inactivo(Request $request)
    {
        $usuario = User::findOrFail($request->id);
        $usuario->{$request->field} = $request->value; 
        $usuario->save();

        return response()->json(['success' => true]);
    }

    // Info de cliente (AJAX)
    public function getCliente(Request $request)
    {
        $cardCode = $request->cardCode;

        $cliente = DB::table('ocrd')
            ->select('CardCode', 'CardName', 'phone1', 'e-mail')
            ->where('CardCode', $cardCode)
            ->first();

        if (!$cliente) {
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }

        $direccionFiscal = DB::table('crd1')
            ->where('CardCode', $cardCode)
            ->where('AdresType', 'S')
            ->first();

        $direccionEnvio = DB::table('crd1')
            ->where('CardCode', $cardCode)
            ->where('AdresType', 'B')
            ->first();

        $data = [
            "CardCode"        => $cliente->CardCode ?? "*SIN DATO*",
            "Nombres"         => $cliente->CardName ?? "*SIN DATO*",
            "ApellidoPaterno" => "*SIN DATO*",
            "ApellidoMaterno" => "*SIN DATO*",
            "Telefono"        => $cliente->phone1 ?? "*SIN DATO*",
            "EmailContacto"   => $cliente->{"e-mail"} ?? "*SIN DATO*",
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

    // Info de vendedor (AJAX)
    public function show($slpCode)
    {
        $vendedor = DB::table('oslp')
            ->select('SlpCode', 'SlpName', 'Active')
            ->where('SlpCode', $slpCode)
            ->where('Active', 'Y')
            ->first();

        if (!$vendedor) {
            return response()->json(['error' => 'Vendedor no encontrado o inactivo'], 404);
        }

        return response()->json($vendedor);
    }
}
