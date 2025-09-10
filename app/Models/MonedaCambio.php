<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonedaCambio extends Model
{
    protected $table = 'ORTT'; // Nombre real de la tabla
    protected $primaryKey = ['RateDate', 'Currency_ID']; // clave compuesta
    public $incrementing = false; // porque no es INT autoincremental
    public $timestamps = false;

    protected $fillable = [
        'Currency_ID',
        'RateDate',
        'Rate',
    ];

    //relacion con moneda a la que pertenece el cambio(OCRN)
    public function moneda()
    {
        return $this->belongsTo(Moneda::class, 'Currency_ID', 'Currency_ID');
    }
}
