<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// Borrar al poner en producción
use App\Models\MonedaCambio;
use Illuminate\Support\Facades\Http;

class UsuarioController extends Controller
{
    // Muestra la lista de usuarios
    public function index()
{
    $usuarios = User::all();

    // Clientes que ya tienen usuario
    $clientesConUsuario = User::whereNotNull('codigo_cliente')
                              ->pluck('codigo_cliente')
                              ->toArray();

    // Vendedores que ya tienen usuario
    $vendedoresConUsuario = User::whereNotNull('codigo_vendedor')
                                ->pluck('codigo_vendedor')
                                ->toArray();

    // Traemos los clientes de OCRD que aún no tienen usuario
    $clientes = DB::table('ocrd')
        ->select('CardCode','CardName')
        ->whereNotIn('CardCode', $clientesConUsuario)
        ->get();

    // Traemos los vendedores activos de OSLP que aún no tienen usuario
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

            $cliente = DB::table('ocrd')->where('CardCode', $request->cliente)->first();

            $user = new User();
            $user->email    = $request->email;
            $user->password = Hash::make($request->password);
            $user->rol_id   = 3; // Rol cliente
            $user->activo   = 1;
            $user->nombre   = $cliente->CardName ?? 'Cliente';

            $user->save();

            return redirect()->route('admin.usuarios.index')
                ->with('success', 'Usuario cliente creado correctamente');
        }

        // Si viene slpcode, es usuario vendedor
        if ($request->has('slpcode')) {
            $request->validate([
                'slpcode'  => 'required',
                'email'    => 'required|email|unique:users,email',
                'password' => 'required|confirmed|min:6',
            ]);

            $vendedor = DB::table('oslp')->where('SlpCode', $request->slpcode)->first();

            $user = new User();
            $user->email    = $request->email;
            $user->password = Hash::make($request->password);
            $user->rol_id   = 4; // Rol vendedor
            $user->activo   = 1;
            $user->nombre   = $vendedor->SlpName ?? 'Vendedor';

            $user->save();

            return redirect()->route('admin.usuarios.index')
                ->with('success', 'Usuario vendedor creado correctamente');
        }

        return redirect()->back()->with('error', 'No se pudo determinar el tipo de usuario a crear.');
    }

    // Devuelve info del cliente (AJAX)
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

    // Devuelve info del vendedor (AJAX)
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

    // Borrar cuando esté en producción
    public function insertarMonedas()
    {
        $hoy = now()->format('Y-m-d');

        $response = Http::get('https://api.frankfurter.app/latest', [
            'from' => 'MXN',
            'to'   => 'USD,EUR'
        ]);

        if ($response->failed()) {
            return redirect()->back()->with('error', 'No se pudo obtener el tipo de cambio.');
        }

        $rates = $response->json()['rates'] ?? null;

        if (!$rates) {
            return redirect()->back()->with('error', 'No se encontraron tipos de cambio.');
        }

        $usdToMxn = 1 / $rates['USD'];
        $eurToMxn = 1 / $rates['EUR'];

        $mensajes = [];

        if (!MonedaCambio::where('Currency_ID', 1)->where('RateDate', $hoy)->exists()) {
            MonedaCambio::create([
                'Currency_ID' => 1,
                'RateDate'    => $hoy,
                'Rate'        => 1.0
            ]);
            $mensajes[] = 'MXN agregado.';
        } else {
            $mensajes[] = 'MXN ya existe para hoy.';
        }

        if (!MonedaCambio::where('Currency_ID', 2)->where('RateDate', $hoy)->exists()) {
            MonedaCambio::create([
                'Currency_ID' => 2,
                'RateDate'    => $hoy,
                'Rate'        => $usdToMxn
            ]);
            $mensajes[] = 'USD→MXN agregado.';
        } else {
            $mensajes[] = 'USD→MXN ya existe para hoy.';
        }

        if (!MonedaCambio::where('Currency_ID', 3)->where('RateDate', $hoy)->exists()) {
            MonedaCambio::create([
                'Currency_ID' => 3,
                'RateDate'    => $hoy,
                'Rate'        => $eurToMxn
            ]);
            $mensajes[] = 'EUR→MXN agregado.';
        } else {
            $mensajes[] = 'EUR→MXN ya existe para hoy.';
        }

        return redirect()->back()->with('success', implode(' ', $mensajes));
    }


    public function activo_inactivo(Request $request)
    {
        $usuario = User::findOrFail($request->id);
        $usuario->{$request->field} = $request->value; 
        $usuario->save();

        return response()->json(['success' => true]);
    }


    
}
