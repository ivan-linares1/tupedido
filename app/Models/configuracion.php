<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class configuracion extends Model
{
    protected $table = 'configuracion';
    public $timestamps = false;

    protected $fillable = [
        'iva',
        'ruta_logo',
        'nombre_empresa',
        'calle',
        'colonia',
        'CP',
        'ciudad',
        'telefono',
        'pais',
        'MonedaPrincipal',
    ];

    public function monedaPrincipal()
    {
        return $this->belongsTo(Moneda::class, 'MonedaPrincipal', 'Currency_ID');
    }

    // Relación con impuesto
    public function impuesto()
    {
        return $this->belongsTo(impuestos::class, 'iva', 'Code');
    }

}
