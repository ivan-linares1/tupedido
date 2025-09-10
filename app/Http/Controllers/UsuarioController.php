<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

//Boorar al poner en produccion
use App\Models\MonedaCambio;
use Illuminate\Support\Facades\Http;

class UsuarioController extends Controller
{
    // Muestra la lista de usuarios
    public function index()
    {
        // Obtener todos los usuarios de la base de datos
        $usuarios = User::all();

        // Retornar la vista y pasar los usuarios
        return view('admin.user', compact('usuarios'));
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
