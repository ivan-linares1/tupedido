<?php

namespace App\Http\Controllers;

use App\Models\configuracion;
use Illuminate\Http\Request;

class configuracionController extends Controller
{
    public function index()
    {
        // Traemos la primera fila
        $configuracion = Configuracion::first();

        // Si no existe, crear un registro por defecto
        if (!$configuracion) {
            $configuracion = Configuracion::create([
                'iva' => 16,
                'ruta_logo' => '',
                'nombre_empresa' => 'KOMBITEC, S.A. DE C.V',
                'calle' => 'AV. DR. SALVADOR NAVA MARTÍNEZ 232',
                'colonia' => 'COL. EL PASEO, SAN LUIS POTOSÍ',
                'CP' => '78320',
                'ciudad' => 'San Luis Potosí',
                'telefono' => '',
                'pais' => 'MEXICO'
            ]);
        }

        return view('admin.configuracion', compact('configuracion'));
    }

    public function update(Request $request)
    {
        $config = configuracion::firstOrFail();

        $request->validate([
            'iva' => 'required|integer|min:0',
            'nombre_empresa' => 'required|string|max:255',
            'calle' => 'nullable|string|max:150',
            'colonia' => 'nullable|string|max:50',
            'CP' => 'nullable|string|max:10',
            'ciudad' => 'nullable|string|max:30',
            'telefono' => 'nullable|string|max:15',
            'pais' => 'nullable|string|max:30',
            'ruta_logo' => 'nullable|image|mimes:jpg,jpeg,png,gif,svg|max:2048',
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
