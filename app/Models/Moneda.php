<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Moneda extends Model
{
    // Nombre de la tabla
    protected $table = 'OCRN';

    // Clave primaria
    protected $primaryKey = 'Currency_ID';
    public $incrementing = true;
    protected $keyType = 'int';

    // La tabla no tiene timestamps
    public $timestamps = false;

    // Campos asignables masivamente
    protected $fillable = [
        'Currency',
        'CurrName'
    ];

    //Relacion de moneda con los precios (ITM1)
    public function precios()
    {
        return $this->hasMany(Precios::class, 'Currency_ID', 'Currency_ID');
    }

    //relacion de la moneda con su valor de cambio (ORTT)
    public function cambios()
    {
        return $this->hasMany(MonedaCambio::class, 'Currency_ID', 'Currency_ID');
    }
}
