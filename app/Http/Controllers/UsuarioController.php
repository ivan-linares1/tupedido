<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

//Boorar al poner en produccion
use App\Models\MonedaCambio;
use Illuminate\Support\Facades\Http;

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




     //borrar cuando este en produccion*****************
    public function insertarMonedas()
    {
        $hoy = now()->format('Y-m-d');

        // Llamada a la API Frankfurter para obtener USD y EUR respecto a MXN
        $response = Http::get('https://api.frankfurter.app/latest', [
            'from' => 'MXN',
            'to' => 'USD,EUR'
        ]);

        if ($response->failed()) {
            return redirect()->back()->with('error', 'No se pudo obtener el tipo de cambio.');
        }

        $rates = $response->json()['rates'] ?? null;

        
        if (!$rates) {
            return redirect()->back()->with('error', 'No se encontraron tipos de cambio.');
        }

        // Invertimos porque la API devuelve 1 MXN = x USD/EUR, necesitamos USD→MXN
        $usdToMxn = 1 / $rates['USD'];
        $eurToMxn = 1 / $rates['EUR'];

        //dd($usdToMxn, $eurToMxn);

        // Guardamos en ORTT
        $mensajes = [];

        // MXN como referencia
        if (!MonedaCambio::where('Currency_ID', 1)->where('RateDate', $hoy)->exists()) {
            MonedaCambio::create([
                'Currency_ID' => 1,
                'RateDate' => $hoy,
                'Rate' => 1.0
            ]);
            $mensajes[] = 'MXN agregado.';
        } else {
            $mensajes[] = 'MXN ya existe para hoy.';
        }

        // USD → MXN
        if (!MonedaCambio::where('Currency_ID', 2)->where('RateDate', $hoy)->exists()) {
            MonedaCambio::create([
                'Currency_ID' => 2,
                'RateDate' => $hoy,
                'Rate' => $usdToMxn
            ]);
            $mensajes[] = 'USD→MXN agregado.';
        } else {
            $mensajes[] = 'USD→MXN ya existe para hoy.';
        }

        // EUR → MXN
        if (!MonedaCambio::where('Currency_ID', 3)->where('RateDate', $hoy)->exists()) {
            MonedaCambio::create([
                'Currency_ID' => 3,
                'RateDate' => $hoy,
                'Rate' => $eurToMxn
            ]);
            $mensajes[] = 'EUR→MXN agregado.';
        } else {
            $mensajes[] = 'EUR→MXN ya existe para hoy.';
        }

        // Concatenamos los mensajes y mostramos en un alert
        return redirect()->back()->with('success', implode(' ', $mensajes));
    }
}
