<?php

namespace App\Http\Controllers;

use App\Models\configuracion;
use App\Models\impuestos;
use App\Models\Moneda;
use Illuminate\Http\Request;

class configuracionController extends Controller
{
    public function index()
    {
        // Traemos la primera fila
        $configuracion = Configuracion::first();
        $monedas = Moneda::all();
        $impuestos = impuestos::where('Lock', 'N')->get();
        
        //si no existen monedas manda a la vista de los sincronizadores para ejecutarlo manualmente 
        if($monedas->isEmpty() || $impuestos->isEmpty())
        {
            return redirect()->route('sincronizadores')->with('error', 'Faltan monedas o impuestos para iniciar el sistema. Por favor, agregue datos faltantes antes de continuar.');
        }

        // Si no existe, crear un registro por defecto
        if (!$configuracion ) {
            // Obtener el primer registro de monedas (o null si no hay ninguno)
            $moneda = Moneda::first();
            $iva = impuestos::first();
            $configuracion = Configuracion::create([
                'iva' => $iva ? $iva->Code : null,
                'ruta_logo' => 'logos/logo.png',
                'nombre_empresa' => 'KOMBITEC, S.A. DE C.V',
                'calle' => 'AV. DR. SALVADOR NAVA MARTÍNEZ 232',
                'colonia' => 'COL. EL PASEO, SAN LUIS POTOSÍ',
                'CP' => '78320',
                'ciudad' => 'San Luis Potosí',
                'telefono' => '4441370770',
                'pais' => 'MEXICO',
                'MonedaPrincipal' => $moneda ? $moneda->Currency_ID : null,
            ]);
        }

        return view('admin.configuracion', compact('configuracion', 'monedas', 'impuestos'));
    }

    public function update(Request $request)
    {
        $config = configuracion::firstOrFail();

        $request->validate([
            'iva' => 'required',
            'nombre_empresa' => 'required|string|max:255',
            'calle' => 'nullable|string|max:150',
            'colonia' => 'nullable|string|max:50',
            'CP' => 'nullable|string|max:10',
            'ciudad' => 'nullable|string|max:30',
            'telefono' => 'nullable|string|max:15',
            'pais' => 'nullable|string|max:30',
            'ruta_logo' => 'nullable|image|mimes:jpg,jpeg,png,gif,svg|max:2048',
            'MonedaPrincipal' => 'nullable',
        ]);

        $data = $request->except('ruta_logo');

        if ($request->hasFile('ruta_logo')) {
            $path = $request->file('ruta_logo')->store('logos', 'public');
            $data['ruta_logo'] = $path;
        }

        $config->update($data);

        return redirect()->back()->with('success', 'Configuración actualizada correctamente');
    }
}
