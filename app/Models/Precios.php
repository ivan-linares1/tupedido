<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Precios extends Model
{
    protected $table = 'ITM1'; // Nombre real de la tabla
    protected $primaryKey = ['ItemCode', 'PriceList']; // clave compuesta
    public $incrementing = false; // porque no es INT autoincremental
    public $timestamps = false;

    protected $fillable = [
        'ItemCode',
        'PriceList',
        'Price',
        'Currency_ID',
    ];

    // Relación: un precio pertenece a un artículo (itm1)
    public function articulo()
    {
        return $this->belongsTo(Articulo::class, 'ItemCode', 'ItemCode');
    }

    // Relacion de precio con la moneda perteneciente (OCRN)
    public function moneda()
    {
        return $this->belongsTo(Moneda::class, 'Currency_ID', 'Currency_ID');
    }


}
