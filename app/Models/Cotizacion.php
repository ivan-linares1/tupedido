<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cotizacion extends Model
{
    use HasFactory;

    protected $table = 'OQUT';
    protected $primaryKey = 'DocEntry';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'CardCode', 
        'DocDate', 
        'DocDueDate', 
        'CardName', 
        'SlpCode',
        'Phone1', 
        'E_Mail', 
        'DocCur', 
        'ShipToCode', 
        'PayToCode',
        'Address', 
        'Address2',
        'TotalSinPromo', 
        'Descuento', 
        'Subtotal',
        'IVA', 
        'Total'
    ];

    // Relación con las líneas de cotización
    public function lineas()
    {
        return $this->hasMany(LineasCotizacion::class, 'DocEntry', 'DocEntry');
    }

    // Relación con el vendedor
    public function vendedor()
    {
        return $this->belongsTo(Vendedores::class, 'SlpCode', 'SlpCode');
    }

    public function moneda()
    {
        return $this->belongsTo(Moneda::class, 'DocCur', 'Currency_ID');
    }
}
